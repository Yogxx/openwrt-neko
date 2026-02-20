<?php
include './cfg.php';
include './devinfo.php';

if (isset($_POST['ajax'])) {

    header('Content-Type: application/json');

    $map = [
        'start'   => 'start',
        'disable' => 'stop',
        'restart' => 'restart'
    ];

    if ($_POST['ajax'] === 'action' && isset($_POST['action'])) {

        if (isset($map[$_POST['action']])) {
            shell_exec("/etc/init.d/neko {$map[$_POST['action']]}");
            sleep(1);
        }

        echo json_encode(['result'=>'ok']);
        exit;
    }

    if ($_POST['ajax'] === 'status') {

        $status_raw  = shell_exec("/etc/init.d/neko status 2>/dev/null");
        $running = (strpos($status_raw,"running") !== false);

        echo json_encode(['running'=>$running]);
        exit;
    }
}

$str_cfg   = substr($selected_config, strlen("$neko_dir/config") + 1);
$core_mode = exec("uci -q get neko.cfg.core_mode");

$status_raw  = shell_exec("/etc/init.d/neko status 2>/dev/null");
$neko_status = (strpos($status_raw,"running") !== false);

$binary_log = ($core_mode === 'mihomo')
    ? "$neko_dir/tmp/mihomo_log.txt"
    : "$neko_dir/tmp/singbox_log.txt";

$core_label = strtoupper($core_mode);
$core_color = ($core_mode === 'mihomo') ? 'primary' : 'success';
$core_icon  = ($core_mode === 'mihomo') ? 'box' : 'cpu';

$show_ip  = exec("uci -q get neko.cfg.show_ip");
$show_isp = exec("uci -q get neko.cfg.show_isp");

include './header.php';
include './navbar.php';
?>

<div class="container p-3">

    <div class="card p-3">

        <div class="text-center mb-4">
            <i data-feather="<?= $core_icon ?>" class="feather-lg text-<?= $core_color ?> mb-2"></i>
            <div class="small text-muted">Active Core</div>
            <h5 class="fw-bold text-<?= $core_color ?>"><?= $core_label ?></h5>
        </div>

        <div class="mb-4">
            <h5>System Information</h5>
            <div class="list-group list-group-flush">

                <?php if($show_ip == '1'): ?>
                <div class="list-group-item d-flex justify-content-between">
                    <span>IP Address</span>
                    <span id="ip-address">Loading...</span>
                </div>
                <?php endif; ?>

                <?php if($show_isp == '1'): ?>
                <div class="list-group-item d-flex justify-content-between">
                    <span>ISP</span>
                    <span id="isp-info">Loading...</span>
                </div>
                <?php endif; ?>

                <div class="list-group-item d-flex justify-content-between">
                    <span>Devices</span>
                    <span><?= $devices ?></span>
                </div>

                <div class="list-group-item d-flex justify-content-between">
                    <span>OS Version</span>
                    <span><?= $OSVer ?></span>
                </div>

                <div class="list-group-item d-flex justify-content-between">
                    <span>Kernel</span>
                    <span><?= $kernelv ?></span>
                </div>

                <div class="list-group-item d-flex justify-content-between">
                    <span>Uptime</span>
                    <span><?= "$hours h $minutes m $seconds s" ?></span>
                </div>

            </div>
        </div>

        <div class="mb-4">
            <h5>Neko</h5>

            <div class="btn-group w-100 mb-3">
                <button id="neko-status-btn"
                    class="btn <?= $neko_status ? 'btn-success' : 'btn-outline-primary' ?>">
                    <?= $neko_status ? 'RUNNING' : 'DISABLED' ?>
                </button>
                <button class="btn btn-warning"><?= $str_cfg ?></button>
            </div>

            <div class="btn-group w-100 mb-3">
                <button id="btn-start"
                    class="btn btn-success"
                    onclick="controlNeko('start')">
                    Enable <?= $core_label ?>
                </button>

                <button id="btn-stop"
                    class="btn btn-primary"
                    onclick="controlNeko('disable')">
                    Disable <?= $core_label ?>
                </button>

                <button id="btn-restart"
                    class="btn btn-warning"
                    onclick="controlNeko('restart')">
                    Restart <?= $core_label ?>
                </button>
            </div>

            <input class="form-control text-center mb-3"
                value="<?= ($core_mode==='mihomo') ? $neko_cfg['enhanced']." | ".$neko_cfg['mode'] : 'SINGBOX | TUN' ?>"
                disabled>
        </div>

        <div class="accordion" id="logAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#logsCollapse">
                        Binary Log
                    </button>
                </h2>
                <div id="logsCollapse" class="accordion-collapse collapse" data-bs-parent="#logAccordion">
                    <div class="accordion-body">

                        <div class="mb-2">
                            <select id="log_filter" class="form-select" onchange="applyFilter()">
                                <option value="ALL">Show All</option>
                                <option value="INFO">INFO</option>
                                <option value="WARN">WARN</option>
                                <option value="ERROR">ERROR</option>
                            </select>
                        </div>

                        <textarea class="form-control mb-3" id="bin_logs" rows="12" readonly></textarea>

                        <button type="button" class="btn btn-primary w-100" onclick="clearLog()">Clear Log</button>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function controlNeko(action){
    fetch('',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'ajax=action&action='+action
    }).then(()=>{ setTimeout(updateStatus,1000); });
}

function updateStatus(){
    fetch('',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'ajax=status'
    })
    .then(r=>r.json())
    .then(data=>{
        const statusBtn  = document.getElementById('neko-status-btn');
        const btnStart   = document.getElementById('btn-start');
        const btnStop    = document.getElementById('btn-stop');
        const btnRestart = document.getElementById('btn-restart');

        if(data.running){
            statusBtn.classList.remove('btn-outline-primary');
            statusBtn.classList.add('btn-success');
            statusBtn.textContent = 'RUNNING';
            btnStart.disabled = true;
            btnStop.disabled = false;
            btnRestart.disabled = false;
        } else {
            statusBtn.classList.remove('btn-success');
            statusBtn.classList.add('btn-outline-primary');
            statusBtn.textContent = 'DISABLED';
            btnStart.disabled = false;
            btnStop.disabled = true;
            btnRestart.disabled = true;
        }
    });
}

document.addEventListener("DOMContentLoaded", function(){
    updateStatus();
});

let fullLogData = '';

function updateLogs() {
    const collapse = document.getElementById('logsCollapse');
    if (!collapse.classList.contains('show')) return;

    fetch('./lib/log.php?data=bin')
        .then(r => r.text())
        .then(data => { fullLogData = data; applyFilter(false); });
}

function applyFilter(save = true) {
    const filter   = document.getElementById('log_filter').value;
    const textarea = document.getElementById('bin_logs');
    if (save) localStorage.setItem("neko_log_filter", filter);

    textarea.value = (filter === "ALL")
        ? fullLogData
        : fullLogData.split('\n').filter(l => l.toUpperCase().includes(filter)).join('\n');

    textarea.scrollTop = textarea.scrollHeight;
}

function clearLog() {
    document.getElementById('bin_logs').value = '';
}

document.addEventListener("DOMContentLoaded", () => {
    const savedFilter = localStorage.getItem("neko_log_filter");
    if (savedFilter) document.getElementById("log_filter").value = savedFilter;

    const savedOpen = localStorage.getItem("neko_log_open");
    if (savedOpen === "true") new bootstrap.Collapse(document.getElementById('logsCollapse'), { toggle: true });

    const logsCollapse = document.getElementById('logsCollapse');
    logsCollapse.addEventListener('shown.bs.collapse', () => { localStorage.setItem("neko_log_open","true"); updateLogs(); });
    logsCollapse.addEventListener('hidden.bs.collapse', () => { localStorage.setItem("neko_log_open","false"); });
    setInterval(updateLogs,5000);

    <?php if($show_ip=='1' || $show_isp=='1'): ?>
    fetch('http://ip-api.com/json/')
        .then(r=>r.json())
        .then(data=>{
            <?php if($show_ip=='1'): ?>document.getElementById('ip-address').textContent = data.query;<?php endif; ?>
            <?php if($show_isp=='1'): ?>document.getElementById('isp-info').textContent = data.isp;<?php endif; ?>
        })
        .catch(()=>{
            <?php if($show_ip=='1'): ?>document.getElementById('ip-address').textContent = 'Failed';<?php endif; ?>
            <?php if($show_isp=='1'): ?>document.getElementById('isp-info').textContent = 'Failed';<?php endif; ?>
        });
    <?php endif; ?>
});
</script>

<?php include './footer.php'; ?>
