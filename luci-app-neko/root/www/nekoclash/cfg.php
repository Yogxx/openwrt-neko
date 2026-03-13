<?php

$neko_dir = "/etc/neko";
$neko_www = "/www/nekoclash";
$core_mode = trim(shell_exec("uci -q get neko.cfg.core_mode"));
$neko_bin  = ($core_mode === 'singbox') ? "/usr/bin/sing-box" : "/usr/bin/mihomo";
$neko_status = trim(shell_exec("uci -q get neko.cfg.enabled"));
$show_ip     = trim(shell_exec("uci -q get neko.cfg.show_ip"));
$show_isp    = trim(shell_exec("uci -q get neko.cfg.show_isp"));
$show_luci   = trim(shell_exec("uci -q get neko.cfg.show_luci"));
$tcp_mode    = trim(shell_exec("uci -q get neko.cfg.tcp_mode")) ?: 'redirect';
$udp_mode    = trim(shell_exec("uci -q get neko.cfg.udp_mode")) ?: 'tun';
$selected_config = trim(shell_exec("uci -q get neko.cfg.selected_config"));

$neko_cfg = [
    'redir' => '', 'port' => '', 'socks' => '', 'mixed' => '',
    'tproxy' => '', 'mode' => '', 'enhanced' => '',
    'secret' => '', 'ext_controller' => ''
];

if (!empty($selected_config) && file_exists($selected_config)) {

    if ($core_mode === 'singbox') {
        $json = @json_decode(file_get_contents($selected_config), true);
        if ($json) {
            // inbounds
            foreach (($json['inbounds'] ?? []) as $ib) {
                $type = $ib['type'] ?? '';
                $port = (string)($ib['listen_port'] ?? '');
                if ($type === 'redirect' || $type === 'redir')  $neko_cfg['redir']  = $port;
                if ($type === 'tproxy')                           $neko_cfg['tproxy'] = $port;
                if ($type === 'mixed')                            $neko_cfg['mixed']  = $port;
                if ($type === 'socks')                            $neko_cfg['socks']  = $port;
                if ($type === 'http')                             $neko_cfg['port']   = $port;
            }
            $clash_api = $json['experimental']['clash_api'] ?? [];
            if (!empty($clash_api['external_controller'])) {
                $neko_cfg['ext_controller'] = $clash_api['external_controller'];
            }
            if (!empty($clash_api['secret'])) {
                $neko_cfg['secret'] = $clash_api['secret'];
            }
            if (!empty($clash_api['default_mode'])) {
                $neko_cfg['mode'] = strtoupper($clash_api['default_mode']);
            }
        }
    } else {
        $yaml = file_get_contents($selected_config);
        $neko_cfg['redir']          = preg_match('/^\s*redir-port:\s*(\S+)\r?/m',         $yaml, $m) ? $m[1] : '';
        $neko_cfg['port']           = preg_match('/^port:\s*(\S+)\r?/m',                   $yaml, $m) ? $m[1] : '';
        $neko_cfg['socks']          = preg_match('/^\s*socks-port:\s*(\S+)\r?/m',         $yaml, $m) ? $m[1] : '';
        $neko_cfg['mixed']          = preg_match('/^\s*mixed-port:\s*(\S+)\r?/m',         $yaml, $m) ? $m[1] : '';
        $neko_cfg['tproxy']         = preg_match('/^\s*tproxy-port:\s*(\S+)\r?/m',        $yaml, $m) ? $m[1] : '';
        $neko_cfg['mode']           = preg_match('/^mode:\s*(\S+)\r?/m',                   $yaml, $m) ? strtoupper($m[1]) : '';
        $neko_cfg['enhanced']       = preg_match('/^\s*enhanced-mode:\s*(\S+)\r?/m',      $yaml, $m) ? strtoupper($m[1]) : '';
        $neko_cfg['secret']         = preg_match('/^secret:\s*(\S+)\r?/m',                 $yaml, $m) ? $m[1] : '';
        $neko_cfg['ext_controller'] = preg_match('/^external-controller:\s*"?(\S+?)"?\r?$/m', $yaml, $m) ? $m[1] : '';
    }
}

// DONT CHANGE THIS FOOTER!!!
$footer = "©2024 <b>signdev</b>";
?>