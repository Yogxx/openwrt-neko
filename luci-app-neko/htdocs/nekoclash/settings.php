<?php

include './cfg.php';

$themeDir = "$neko_www/assets/theme";
$arrFiles = glob("$themeDir/*.css");

for($x=0;$x<count($arrFiles);$x++) 
    $arrFiles[$x] = substr($arrFiles[$x], strlen($themeDir)+1);

$service_check = trim(shell_exec("/etc/init.d/neko status 2>/dev/null | grep running"));
$is_running = !empty($service_check);

if(isset($_POST['themechange'])){
    $dt = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $_POST['themechange']);
    if (!empty($dt)) {
        shell_exec("uci set neko.cfg.theme=" . escapeshellarg($dt) . " && uci commit neko");
    }
}

if(isset($_POST['core_mode'])){

    if($is_running){
        $error_msg = "Neko is running. Stop service to change core.";
    } else {

        $allowed = ['mihomo','singbox'];
        $dt = $_POST['core_mode'];

        if(in_array($dt, $allowed)){
            shell_exec("uci set neko.cfg.core_mode=" . escapeshellarg($dt) . " && uci commit neko");
            $cfg_dir = '/etc/neko/config';
            $ext     = ($dt === 'singbox') ? '*.json' : '*.yaml';
            $files   = glob("$cfg_dir/$ext") ?: [];
            $new_cfg = !empty($files) ? $files[0] : '';
            shell_exec("uci set neko.cfg.selected_config=" . escapeshellarg($new_cfg) . " && uci commit neko");
        }
    }
}

if(isset($_POST['show_ip'])){
    shell_exec("uci set neko.cfg.show_ip='".intval($_POST['show_ip'])."' && uci commit neko");
}

if(isset($_POST['show_isp'])){
    shell_exec("uci set neko.cfg.show_isp='".intval($_POST['show_isp'])."' && uci commit neko");
}

if(isset($_POST['show_luci'])){
    shell_exec("uci set neko.cfg.show_luci='".intval($_POST['show_luci'])."' && uci commit neko");
}

if(isset($_POST['auto_start'])){
    $val = intval($_POST['auto_start']);
    shell_exec("uci set neko.cfg.enabled=" . escapeshellarg($val) . " && uci commit neko");
    if ($val === 1) {
        shell_exec("/etc/init.d/neko enable 2>/dev/null");
    } else {
        shell_exec("/etc/init.d/neko disable 2>/dev/null");
    }
}

function patch_mihomo_config($config_file, $tcp_mode, $udp_mode) {
    if (!file_exists($config_file)) return;

    $need_tun        = ($tcp_mode === 'tun' || $udp_mode === 'tun');
    $need_redir_port = ($tcp_mode === 'redirect');
    $f = escapeshellarg($config_file);

    $tun_enable = $need_tun ? 'true' : 'false';
    shell_exec("sed -i '/^tun:/,/^[a-z]/{s/^  enable: .*/  enable: $tun_enable/}' $f");
    shell_exec("sed -i '/^tun:/,/^[a-z]/{s/^  auto-redirect: .*/  auto-redirect: false/}' $f");
    shell_exec("sed -i '/^tun:/,/^[a-z]/{s/^  auto-route: .*/  auto-route: false/}' $f");
    shell_exec("sed -i '/^tun:/,/^[a-z]/{s/^  auto-detect-interface: .*/  auto-detect-interface: false/}' $f");

    if ($need_redir_port) {
        shell_exec("sed -i 's/^#redir-port:/redir-port:/' $f");
    } else {
        shell_exec("sed -i 's/^redir-port:/#redir-port:/' $f");
    }
}

function patch_singbox_config($config_file, $tcp_mode, $udp_mode) {
    if (!file_exists($config_file)) return;

    $json = @json_decode(file_get_contents($config_file), true);
    if (!$json) return;

    $need_redirect = ($tcp_mode === 'redirect');
    $need_tproxy   = ($tcp_mode === 'tproxy' || $udp_mode === 'tproxy');
    $need_tun      = ($tcp_mode === 'tun' || $udp_mode === 'tun');

    foreach ($json['inbounds'] as &$ib) {
        $type = $ib['type'] ?? '';
        if ($type === 'redirect') {
            if (!$need_redirect) $ib['disabled'] = true;
            else unset($ib['disabled']);
        }
        if ($type === 'tproxy') {
            if (!$need_tproxy) $ib['disabled'] = true;
            else unset($ib['disabled']);
        }
        if ($type === 'tun') {
            if (!$need_tun) {
                $ib['disabled'] = true;
            } else {
                unset($ib['disabled']);
                // auto_route dan auto_redirect selalu false — nftables yang handle
                $ib['auto_route']    = false;
                $ib['auto_redirect'] = false;
            }
        }
    }
    unset($ib);

    file_put_contents($config_file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if(isset($_POST['tcp_mode']) || isset($_POST['udp_mode'])){
    $new_tcp = isset($_POST['tcp_mode']) ? $_POST['tcp_mode'] : $tcp_mode;
    $new_udp = isset($_POST['udp_mode']) ? $_POST['udp_mode'] : $udp_mode;

    $allowed_tcp = ['redirect', 'tproxy', 'tun', 'disable'];
    $allowed_udp = ['tun', 'tproxy', 'disable'];

    if(in_array($new_tcp, $allowed_tcp)){
        shell_exec("uci set neko.cfg.tcp_mode=" . escapeshellarg($new_tcp) . " && uci commit neko");
        $tcp_mode = $new_tcp;
    }
    if(in_array($new_udp, $allowed_udp)){
        shell_exec("uci set neko.cfg.udp_mode=" . escapeshellarg($new_udp) . " && uci commit neko");
        $udp_mode = $new_udp;
    }

    if(!empty($selected_config) && file_exists($selected_config)){
        $ext = strtolower(pathinfo($selected_config, PATHINFO_EXTENSION));
        if($core_mode === 'mihomo' && $ext === 'yaml') {
            patch_mihomo_config($selected_config, $tcp_mode, $udp_mode);
        } elseif($core_mode === 'singbox' && $ext === 'json') {
            patch_singbox_config($selected_config, $tcp_mode, $udp_mode);
        }
    }
}

$current_core = trim(shell_exec("uci -q get neko.cfg.core_mode"));
$show_ip      = trim(shell_exec("uci -q get neko.cfg.show_ip"));
$show_isp     = trim(shell_exec("uci -q get neko.cfg.show_isp"));
$show_luci    = trim(shell_exec("uci -q get neko.cfg.show_luci"));
$auto_start   = trim(shell_exec("uci -q get neko.cfg.enabled")) ?: '0';
$is_enabled   = trim(shell_exec("/etc/init.d/neko enabled 2>/dev/null; echo $?")) === '0';

?>

<?php include './header.php'; ?>
<?php include './navbar.php'; ?>

<div class="container p-3">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <i data-feather="settings" class="feather-sm me-2"></i>
            <h5 class="card-title mb-0">Core Settings</h5>
        </div>
        <div class="card-body">

            <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger">
                <?php echo $error_msg; ?>
            </div>
            <?php endif; ?>

            <?php if($is_running): ?>
            <div class="alert alert-info text-center">
                <i data-feather="lock" class="feather-sm me-2"></i>
                <span class="fw-bold text-uppercase">
                    <?php echo $current_core; ?>
                </span>
            </div>
            <?php endif; ?>

            <form action="settings.php" method="post">
                <div class="mb-3">
                    <label class="form-label">Select Core Mode:</label>
                    <select name="core_mode"
                        class="form-select"
                        <?php if($is_running) echo 'disabled style="opacity:.6;cursor:not-allowed;"'; ?>>
                        <option value="mihomo" <?php if($current_core=='mihomo') echo 'selected'; ?>>Core Mihomo</option>
                        <option value="singbox" <?php if($current_core=='singbox') echo 'selected'; ?>>Core Singbox</option>
                    </select>
                    <?php if($is_running): ?>
                    <div class="form-text text-warning mt-2">
                        <i data-feather="alert-triangle" class="feather-xs me-1"></i>
                        Stop service to change core.
                    </div>
                    <?php endif; ?>
                </div>

                <?php if(!$is_running): ?>
                <button type="submit" class="btn btn-primary">Save</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div class="container p-3">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <i data-feather="settings" class="feather-sm me-2"></i>
            <h5 class="card-title mb-0">Neko Settings</h5>
        </div>
        <div class="card-body">

            <form action="settings.php" method="post">

                <div class="mb-3">
                    <label class="form-label">Show IP Address:</label>
                    <select name="show_ip" class="form-select">
                        <option value="1" <?php if($show_ip=='1') echo 'selected'; ?>>Enable</option>
                        <option value="0" <?php if($show_ip=='0') echo 'selected'; ?>>Disable</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Show ISP:</label>
                    <select name="show_isp" class="form-select">
                        <option value="1" <?php if($show_isp=='1') echo 'selected'; ?>>Enable</option>
                        <option value="0" <?php if($show_isp=='0') echo 'selected'; ?>>Disable</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Show Neko on LuCI:</label>
                    <select name="show_luci" class="form-select">
                        <option value="1" <?php if($show_luci=='1') echo 'selected'; ?>>Enable</option>
                        <option value="0" <?php if($show_luci=='0') echo 'selected'; ?>>Disable</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Auto Start on Boot:</label>
                    <select name="auto_start" class="form-select">
                        <option value="1" <?php if($auto_start=='1') echo 'selected'; ?>>Enable</option>
                        <option value="0" <?php if($auto_start=='0') echo 'selected'; ?>>Disable</option>
                    </select>
                    <div class="form-text mt-1">
                        <i data-feather="info" class="feather-xs me-1"></i>
                        Status initd:
                        <?php if($is_enabled): ?>
                            <span class="badge bg-success">Enabled</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Disabled</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">TCP Mode:</label>
                    <select name="tcp_mode" class="form-select"
                        <?php if($is_running) echo 'disabled style="opacity:.6;cursor:not-allowed;"'; ?>>
                        <option value="redirect" <?php if($tcp_mode=='redirect') echo 'selected'; ?>>Redirect Mode</option>
                        <option value="tproxy"   <?php if($tcp_mode=='tproxy')   echo 'selected'; ?>>TPROXY Mode</option>
                        <option value="tun"      <?php if($tcp_mode=='tun')      echo 'selected'; ?>>TUN Mode</option>
                        <option value="disable"  <?php if($tcp_mode=='disable')  echo 'selected'; ?>>Disable</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">UDP Mode:</label>
                    <select name="udp_mode" class="form-select"
                        <?php if($is_running) echo 'disabled style="opacity:.6;cursor:not-allowed;"'; ?>>
                        <option value="tun"      <?php if($udp_mode=='tun')     echo 'selected'; ?>>TUN Mode</option>
                        <option value="tproxy"   <?php if($udp_mode=='tproxy')  echo 'selected'; ?>>TPROXY Mode</option>
                        <option value="disable"  <?php if($udp_mode=='disable') echo 'selected'; ?>>Disable</option>
                    </select>
                    <?php if($is_running): ?>
                    <div class="form-text text-warning mt-2">
                        <i data-feather="alert-triangle" class="feather-xs me-1"></i>
                        Stop service to change mode.
                    </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Save</button>
            </form>

        </div>
    </div>
</div>

<div class="container p-3">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <i data-feather="info" class="feather-sm me-2"></i>
            <h5 class="card-title mb-0">Software Information</h5>
        </div>
        <div class="card-body">

            <table class="table table-borderless mb-3">
                <tbody>

                    <tr>
                        <td class="col-3">Client Version</td>
                        <td class="col-9">
                            <div class="form-control text-center position-relative" id="cliver">Loading...</div>
                            <div class="mt-1 text-center small d-none" id="neko-update-info">
                                Latest: <span id="neko-latest" class="fw-bold"></span>
                                <span id="neko-badge" class="badge bg-warning text-dark ms-2">Update Available</span>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="col-3">Mihomo Version</td>
                        <td class="col-9">
                            <div class="form-control text-center" id="mihomover">-</div>
                        </td>
                    </tr>

                    <tr>
                        <td class="col-3">Sing-box Version</td>
                        <td class="col-9">
                            <div class="form-control text-center" id="singboxver">-</div>
                        </td>
                    </tr>

                </tbody>
            </table>

        </div>
    </div>
</div>

<div class="container p-3">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <i data-feather="info" class="feather-sm me-2"></i>
            <h5 class="card-title mb-0">About</h5>
        </div>
        <div class="card-body">
            <div class="text-center">
                <h5 class="mb-3">NekoClash</h5>
                <p>NekoClash is a family friendly Clash Proxy tool, this tool makes it easy for users to use Clash Proxy, inspired by OpenClash Tools. NekoClash has writen by PHP.</p>
                <p>This tool aims to make it easier to use Clash Proxy</p>
                <p>If you have questions or feedback about NekoClash you can contact me on the <span class="badge bg-indigo"><b>DBAI Discord Server</b></span> link below</p>

                <h5 class="mb-3">External Links</h5>
                <div class="container mb-4">
                    <div class="row g-3 justify-content-center">

                        <div class="col-md-6">
                            <div class="d-grid">
                                <a class="btn btn-outline-info" target="_blank" href="https://discord.gg/vtV5QSq6D6">
                                    <i data-feather="message-circle" class="feather-sm me-2"></i>
                                    DBAI Discord
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-grid">
                                <a class="btn btn-outline-info" target="_blank" href="https://github.com/nosignals">
                                    <i data-feather="github" class="feather-sm me-2"></i>
                                    Nosignals
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-grid">
                                <a class="btn btn-outline-info" target="_blank" href="https://www.facebook.com/groups/indowrt">
                                    <i data-feather="facebook" class="feather-sm me-2"></i>
                                    indoWRT Group
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-grid">
                                <a class="btn btn-outline-info" target="_blank" href="https://github.com/bobbyunknown">
                                    <i data-feather="github" class="feather-sm me-2"></i>
                                    BobbyUnknown
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-grid">
                                <a class="btn btn-outline-info" target="_blank" href="https://github.com/MetaCubeX/mihomo">
                                    <i data-feather="box" class="feather-sm me-2"></i>
                                    Mihomo
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-grid">
                                <a class="btn btn-outline-info" target="_blank" href="https://github.com/SagerNet/sing-box">
                                    <i data-feather="cpu" class="feather-sm me-2"></i>
                                    Singbox
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <p class="mb-0">Please don't <b>CHANGE</b> or <b>REMOVE</b> this Credit!.</p>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('./lib/version.php')
    .then(r=>r.json())
    .then(data=>{
        document.getElementById('cliver').textContent = data.neko.current || '-';
        document.getElementById('mihomover').textContent = data.mihomo || '-';
        document.getElementById('singboxver').textContent = data.singbox || '-';

        const updateInfo = document.getElementById('neko-update-info');
        const latestEl   = document.getElementById('neko-latest');
        const badgeEl    = document.getElementById('neko-badge');

        if (data.neko.latest) {
            latestEl.textContent = data.neko.latest;
            updateInfo.classList.remove('d-none');

            if (data.neko.needsUpdate) {
                badgeEl.classList.remove('d-none');
            } else {
                badgeEl.classList.add('d-none');
            }
        }
    })
    .catch(()=>{
        document.getElementById('cliver').textContent = 'Failed';
    });
});
</script>
