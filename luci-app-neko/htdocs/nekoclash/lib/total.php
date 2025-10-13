<?php
$cmd = "ubus call luci getRealtimeStats '{\"device\":\"Meta\", \"mode\":\"interface\"}'";
$response = shell_exec($cmd);
$data = json_decode($response, true);

if ($data && isset($data['result'])) {
    $stats = end($data['result']);
    $total_down = $stats[1];
    $total_up = $stats[3];
    
    echo json_encode([
        'download' => $total_down,
        'upload' => $total_up
    ]);
} else {
    echo json_encode([
        'download' => 0,
        'upload' => 0
    ]);
}
?>