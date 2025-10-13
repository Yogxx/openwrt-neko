<?php
$stat = trim(shell_exec("uci get neko.cfg.enabled"));
if ($stat == "1") {
    $tmp = intval(trim(shell_exec("cat /sys/class/net/Meta/statistics/tx_bytes")));
} else {
    $tmp = 0;
}

if ($tmp < 1024) {
    $data = number_format($tmp, 0) . " B";
} elseif ($tmp < 1048576) {
    $data = number_format($tmp / 1024, 1) . " KB";
} elseif ($tmp < 1073741824) {
    $data = number_format($tmp / 1048576, 1) . " MB";
} else {
    $data = number_format($tmp / 1073741824, 2) . " GB";
}

echo $data;
?>
