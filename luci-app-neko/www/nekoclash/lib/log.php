<?php
include '../cfg.php';

$core_mode = trim(shell_exec("uci -q get neko.cfg.core_mode"));
$host_now = $_SERVER['SERVER_NAME'];

if(isset($_GET['data'])){
    $dt = $_GET['data'];

    if ($dt == 'neko') {
        echo shell_exec("logread | grep neko | tail -n 100");
    }

    else if($dt == 'bin') {
        if ($core_mode === 'mihomo') {
            echo shell_exec("logread | grep mihomo | tail -n 100");
        } elseif ($core_mode === 'singbox') {
            echo shell_exec("logread | grep sing-box | tail -n 100");
        }
    }

    else if($dt == 'neko_ver') {
        echo trim(shell_exec("/etc/init.d/neko status >/dev/null 2>&1 && echo 'Service Active' || echo 'Service Stopped'"));
    }

    else if($dt == 'mihomo_ver') {
        echo trim(shell_exec("/usr/bin/mihomo -v 2>/dev/null | head -1"));
    }

    else if($dt == 'singbox_ver') {
        echo trim(shell_exec("/usr/bin/sing-box version 2>/dev/null | head -1"));
    }

    else if($dt == 'url_dash'){
        header("Content-type: application/json; charset=utf-8");

        $yacd = trim(shell_exec("curl -m 5 -f -s http://$host_now/nekoclash/dashboard.php | grep 'href=\"h' | cut -d '\"' -f6 | head -1"));
        $zashboard = trim(shell_exec("curl -m 5 -f -s http://$host_now/nekoclash/dashboard.php | grep 'href=\"h' | cut -d '\"' -f6 | tail -1"));

        echo json_encode([
            "yacd" => $yacd,
            "zashboard" => $zashboard
        ]);
    }
}
?>
