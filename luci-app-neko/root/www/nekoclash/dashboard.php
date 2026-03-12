<?php
include './cfg.php';

function get_dashboard_config($core_mode, $selected_config, $neko_dir) {
    if ($core_mode === 'mihomo') {
        $port = trim(shell_exec("uci -q get neko.cfg.port")) ?: '9090';
        $secret = '';
        if (!empty($selected_config) && file_exists($selected_config)) {
            $yaml = file_get_contents($selected_config);
            if (preg_match('/secret:\s*(.+)/', $yaml, $m)) {
                $secret = trim($m[1]);
            }
        }
        return ['port' => $port, 'secret' => $secret];

    } elseif ($core_mode === 'singbox') {
        $config_file = "$neko_dir/config/config.json";
        if (file_exists($config_file)) {
            $config    = json_decode(file_get_contents($config_file), true);
            $clash_api = $config['experimental']['clash_api'] ?? [];
            $controller = $clash_api['external_controller'] ?? '0.0.0.0:9090';
            $port      = explode(':', $controller)[1] ?? '9090';
            return ['port' => $port, 'secret' => $clash_api['secret'] ?? ''];
        }
    }

    return ['port' => '9090', 'secret' => ''];
}

$config = get_dashboard_config($core_mode, $selected_config, $neko_dir);
$port   = $config['port'];
$secret = $config['secret'];
$host   = $_SERVER['HTTP_HOST'];

$query          = "?hostname=$host&port=$port&secret=" . urlencode($secret);
$yacd_link      = "http://$host:$port/ui/yacd/$query";
$zashboard_link = "http://$host:$port/ui/zashboard/$query";

include './header.php';
include './navbar.php';
?>
<div class="container p-3">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <i data-feather="monitor" class="feather-sm me-2"></i>
            <h5 class="card-title mb-0">Dashboard</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <div class="d-grid gap-2 d-flex justify-content-center">
                    <?php if ($core_mode === 'mihomo'): ?>
                        <a class="btn btn-outline-primary" target="_blank" href="<?= $yacd_link ?>">
                            <i data-feather="external-link" class="feather-sm me-2"></i>
                            YACD-PANEL
                        </a>
                        <a class="btn btn-outline-primary" target="_blank" href="<?= $zashboard_link ?>">
                            <i data-feather="external-link" class="feather-sm me-2"></i>
                            ZASHBOARD
                        </a>
                    <?php else: ?>
                        <a class="btn btn-outline-primary" target="_blank" href="<?= $zashboard_link ?>">
                            <i data-feather="external-link" class="feather-sm me-2"></i>
                            ZASHBOARD
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <iframe
                    class="border rounded w-100"
                    height="700"
                    src="<?= $zashboard_link ?>"
                    title="zashboard"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>
<?php include './footer.php'; ?>
