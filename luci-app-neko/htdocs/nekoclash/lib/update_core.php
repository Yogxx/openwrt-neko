<?php
/**
 * MIT License
 *
 * Copyright (c) 2024 Nosignal <https://github.com/nosignals>
 * 
 * Contributors:
 * - bobbyunknown <https://github.com/bobbyunknown>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

header('Content-Type: application/json');

function execCommand($cmd) {
    return shell_exec($cmd);
}

if (isset($_GET['action']) && $_GET['action'] === 'get_versions') {
    $type = $_GET['type'] ?? '';
    
    switch($type) {
        case 'mihomo':
            $versions = execCommand('/etc/neko/core/core_update list_mihomo_versions');
            break;
        case 'singbox':
            $versions = execCommand('/etc/neko/core/core_update list_singbox_versions');
            break;
        default:
            die(json_encode(['error' => 'Invalid type']));
    }
    
    if ($versions) {
        $versions_array = array_filter(explode("\n", trim($versions)));
        echo json_encode(['versions' => $versions_array]);
    } else {
        echo json_encode(['error' => 'Failed to get versions']);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'get_log') {
    $log_file = '/etc/neko/tmp/neko_update_log.txt';
    if (file_exists($log_file)) {
        echo json_encode(['log' => file_get_contents($log_file)]);
    } else {
        echo json_encode(['log' => 'Log file not found']);
    }
    exit;
}

if (!isset($_POST['type']) || !isset($_POST['version'])) {
    die(json_encode(['error' => 'Missing parameters']));
}

$type = $_POST['type'];
$version = $_POST['version'];

if (!in_array($type, ['mihomo', 'singbox'])) {
    die(json_encode(['error' => 'Invalid type: ' . $type]));
}

$command = "/etc/neko/core/core_update list_{$type}_versions";
$versions = array_filter(explode("\n", trim(execCommand($command))));

$versionIndex = array_search($version, $versions);
if ($versionIndex === false) {
    die(json_encode(['error' => 'Version not found in available versions']));
}

$choice = ($type === 'mihomo') ? '1' : '2';
$command = sprintf('echo -e "%s\n%d\n" | /etc/neko/core/core_update 2>&1', $choice, $versionIndex + 1);
$output = execCommand($command);

if ($output === null) {
    die(json_encode(['error' => 'Command execution failed']));
}

if (strpos($output, 'Invalid') !== false) {
    die(json_encode(['error' => 'Update failed: ' . $output]));
}

echo json_encode([
    'success' => true,
    'output' => $output
]);