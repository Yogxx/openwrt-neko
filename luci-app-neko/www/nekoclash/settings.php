<?php

include './cfg.php';

$themeDir = "$neko_www/assets/theme";
$arrFiles = glob("$themeDir/*.css");

for($x=0;$x<count($arrFiles);$x++) 
    $arrFiles[$x] = substr($arrFiles[$x], strlen($themeDir)+1);

$service_check = trim(shell_exec("/etc/init.d/neko status 2>/dev/null | grep running"));
$is_running = !empty($service_check);

if(isset($_POST['themechange'])){
    $dt = $_POST['themechange'];
    shell_exec("uci set neko.cfg.theme='$dt' && uci commit neko");
}

if(isset($_POST['fw'])){
    $dt = $_POST['fw'];
    if ($dt == 'enable')  shell_exec("uci set neko.cfg.new_interface='1' && uci commit neko");
    if ($dt == 'disable') shell_exec("uci set neko.cfg.new_interface='0' && uci commit neko");
}

if(isset($_POST['core_mode'])){

    if($is_running){
        $error_msg = "Neko sedang running. Stop service terlebih dahulu sebelum mengganti core.";
    } else {

        $allowed = ['mihomo','singbox'];
        $dt = $_POST['core_mode'];

        if(in_array($dt, $allowed)){
            shell_exec("uci set neko.cfg.core_mode='$dt' && uci commit neko");
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

$current_core = trim(shell_exec("uci -q get neko.cfg.core_mode"));
$show_ip      = trim(shell_exec("uci -q get neko.cfg.show_ip"));
$show_isp     = trim(shell_exec("uci -q get neko.cfg.show_isp"));
$show_luci    = trim(shell_exec("uci -q get neko.cfg.show_luci"));
$fwstatus     = trim(shell_exec("uci -q get neko.cfg.new_interface"));

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
                        <td class="col-2">Auto Reload Firewall</td>
                        <form action="settings.php" method="post">
                            <td class="d-grid">
                                <div class="btn-group col" role="group">
                                    <button type="submit" name="fw" value="enable"
                                        class="btn btn<?php if($fwstatus==1) echo "-outline"; ?>-success <?php if($fwstatus==1) echo "disabled"; ?>">
                                        Enable
                                    </button>
                                    <button type="submit" name="fw" value="disable"
                                        class="btn btn<?php if($fwstatus==0) echo "-outline"; ?>-warning <?php if($fwstatus==0) echo "disabled"; ?>">
                                        Disable
                                    </button>
                                </div>
                            </td>
                        </form>
                    </tr>

                    <tr>
                        <td class="col-3">Client Version</td>
                        <td class="col-9">
                            <div class="form-control text-center" id="cliver">Loading...</div>
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
                <p>NekoClash is a family friendly Clash Proxy tool, this tool makes it easy for users to use Clash Proxy, and User can modify your own Theme based Bootstrap, inspired by OpenClash Tools. NekoClash has writen by PHP, and BASH.</p>
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
    })
    .catch(()=>{
        document.getElementById('cliver').textContent='Failed';
    });
});
</script>
