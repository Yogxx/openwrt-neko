<?php
include '../cfg.php';

$host_now = $_SERVER['SERVER_NAME'];

if (isset($_GET['data'])) {

    $allowed = ['neko', 'bin', 'url_dash'];
    $dt      = $_GET['data'];

    if (!in_array($dt, $allowed)) {
        http_response_code(400);
        echo "Invalid request";
        exit;
    }

    if ($dt == 'neko') {
        echo shell_exec("logread | grep neko | tail -n 100");
    }

    else if ($dt == 'bin') {
        if ($core_mode === 'mihomo') {
            echo shell_exec("logread | grep mihomo | tail -n 100");
        } elseif ($core_mode === 'singbox') {
            echo shell_exec("logread | grep sing-box | tail -n 100");
        }
    }

    else if ($dt == 'url_dash') {
        header("Content-type: application/json; charset=utf-8");

        $host   = $_SERVER['HTTP_HOST'];
        $port   = '9090';
        $secret = '';

        if ($core_mode === 'mihomo') {
            $port = trim(shell_exec("uci -q get neko.cfg.port")) ?: '9090';
            if (!empty($selected_config) && file_exists($selected_config)) {
                $yaml = file_get_contents($selected_config);
                if (preg_match('/secret:\s*(.+)/', $yaml, $m)) {
                    $secret = trim($m[1]);
                }
            }
        } elseif ($core_mode === 'singbox') {
            $config_file = "$neko_dir/config/config.json";
            if (file_exists($config_file)) {
                $config     = json_decode(file_get_contents($config_file), true);
                $clash_api  = $config['experimental']['clash_api'] ?? [];
                $controller = $clash_api['external_controller'] ?? '0.0.0.0:9090';
                $port       = explode(':', $controller)[1] ?? '9090';
                $secret     = $clash_api['secret'] ?? '';
            }
        }

        $query      = "?hostname=$host&port=$port&secret=" . urlencode($secret);
        $base       = "http://$host:$port";

        echo json_encode([
            "yacd"      => $base . "/ui/yacd/"      . $query,
            "zashboard" => $base . "/ui/zashboard/" . $query
        ]);
    }
}
?>
