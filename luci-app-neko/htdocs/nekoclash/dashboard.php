<?php
include './cfg.php';

$config = get_dash_config($core_mode, $neko_cfg, $neko_dir);
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
