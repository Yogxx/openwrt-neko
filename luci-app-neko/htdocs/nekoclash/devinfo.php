<?php

$dt      = json_decode(shell_exec("ubus call system board"), true);
$devices = $dt['model'] ?? 'Unknown';
$OSVer   = isset($dt['release']['distribution'], $dt['release']['version'])
           ? $dt['release']['distribution'] . " " . $dt['release']['version']
           : 'Unknown';

$kernelv = trim(file_get_contents("/proc/sys/kernel/ostype"))
         . ' '
         . trim(file_get_contents("/proc/sys/kernel/osrelease"));

$raw_uptime = (float) explode(" ", file_get_contents("/proc/uptime"))[0];
$hours      = floor($raw_uptime / 3600);
$minutes    = floor(($raw_uptime / 60) % 60);
$seconds    = floor($raw_uptime % 60);

?>
