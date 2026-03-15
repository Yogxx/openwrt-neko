<?php
ob_start();
date_default_timezone_set('UTC');

include './cfg.php';

$cfg_path   = "/etc/neko/config";
$proxy_path = "/etc/neko/proxy_provider";
$rule_path  = "/etc/neko/rule_provider";

$allowed_dirs = [
    realpath($cfg_path),
    realpath($proxy_path),
    realpath($rule_path),
];

function is_path_allowed($file_path, $allowed_dirs) {
    $real = realpath($file_path);
    if ($real === false) {
        $real = realpath(dirname($file_path));
        if ($real === false) return false;
    }
    foreach ($allowed_dirs as $dir) {
        if ($dir !== false && strpos($real, $dir) === 0) return true;
    }
    return false;
}

function create_table($path) {
    $arr_table = array_merge(glob("$path/*.yaml") ?: [], glob("$path/*.txt") ?: [], glob("$path/*.list") ?: []);
    $output = "";

    foreach ($arr_table as $file) {
        $file_dir  = basename(dirname($file));
        $file_name = explode(".", basename($file));

        $output .= "<tr class=\"text-center\">\n";
        $output .= "<td class=\"col-4\">".basename($file)."<br>[ "
            .formatSize(filesize($file))." - "
            .date('Y-m-d H:i:s', filemtime($file))
            ." ]</td>\n";

        $output .= "<td class=\"col-2\">\n";
        $output .= "<form action=\"configs.php\" method=\"post\">\n";
        $output .= "<div class=\"btn-group\" role=\"group\">\n";
        $output .= "<button type=\"submit\" name=\"file_action\" value=\"down@".$file."\" class=\"btn btn-info d-grid\"><i class=\"fa fa-download\"></i> Download</button>\n";
        $file_id = str_replace('.', '_', basename($file));
        $output .= "<button type=\"button\" class=\"btn btn-primary d-grid\" data-bs-toggle=\"modal\" data-bs-target=\"#".$file_dir."_".$file_id."\"><i class=\"fa fa-gear\"></i> Option</button>\n";
        $output .= "</div>\n</form>\n</td>\n</tr>\n";
    }

    return $output;
}

function create_modal($path) {
    $output = "";
    $arr_modal = array_merge(glob("$path/*.yaml") ?: [], glob("$path/*.txt") ?: [], glob("$path/*.list") ?: []);

    foreach ($arr_modal as $file) {
        $file_dir  = basename(dirname($file));
        $file_name = explode(".", basename($file));

        $file_id = str_replace('.', '_', basename($file));
        $output .= "<div class=\"modal fade\" id=\"".$file_dir."_".$file_id."\" tabindex=\"-1\">\n";
        $output .= "<div class=\"modal-dialog modal-xl modal-fullscreen-md-down\">\n";
        $output .= "<div class=\"modal-content\">\n";
        $output .= "<div class=\"modal-header\">\n";
        $output .= "<h5 class=\"modal-title\">".basename($file)."</h5>\n";
        $output .= "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>\n";
        $output .= "</div>\n";
        $output .= "<div class=\"modal-body\">\n";
        $output .= "<textarea id=\"content_".$file_dir."_".$file_id."\" class=\"form-control\" rows=\"15\">"
            .htmlspecialchars(file_get_contents($file)).
            "</textarea>\n</div>\n";

        $output .= "<div class=\"modal-footer\">\n";
        $output .= "<button type=\"button\" class=\"btn btn-danger\" onclick=\"deleteFile('".$file."')\">Delete</button>\n";
        $output .= "<button type=\"button\" class=\"btn btn-success\" onclick=\"saveFile('".$file."', 'content_".$file_dir."_".$file_id."')\">Save</button>\n";
        $output .= "<button type=\"button\" class=\"btn btn-info\" onclick=\"downloadFile('".$file."')\">Download</button>\n";
        $output .= "<button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Close</button>\n";
        $output .= "</div>\n</div>\n</div>\n</div>\n";
    }

    return $output;
}

function up_controller($dir, $allowed_dirs) {
    header('Content-Type: application/json');
    try {
        $real_dir = realpath($dir);
        if ($real_dir === false || !in_array($real_dir, $allowed_dirs))
            throw new Exception("Invalid upload directory");

        if (!isset($_FILES["file_upload"]) || $_FILES["file_upload"]["error"] !== UPLOAD_ERR_OK)
            throw new Exception("Failed to upload file");

        $max_size = 50 * 1024 * 1024;
        if ($_FILES["file_upload"]["size"] > $max_size)
            throw new Exception("File too large, maximum 50MB allowed");

        $target_file = $real_dir . "/" . basename($_FILES["file_upload"]["name"]);
        $fileType    = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!in_array($fileType, ['yaml', 'yml', 'txt', 'list']))
            throw new Exception("Only .yaml, .yml, .txt, .list allowed");

        if (strpos(basename($target_file), ' ') !== false)
            throw new Exception("Filename cannot contain spaces");

        if (!move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file))
            throw new Exception("Upload failed");

        echo json_encode([
            'status'  => 'success',
            'message' => 'Upload success'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

function action_controller($action_str, $allowed_dirs) {
    $action = explode("@", $action_str, 2);
    if (count($action) != 2)
        return ['status' => 'error', 'message' => 'Invalid format'];

    $command   = $action[0];
    $file_path = $action[1];

    if (!is_path_allowed($file_path, $allowed_dirs))
        return ['status' => 'error', 'message' => 'Access denied'];

    if (!file_exists($file_path))
        return ['status' => 'error', 'message' => 'File not found'];

    switch ($command) {
        case "down":
            while (ob_get_level()) ob_end_clean();
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
            header('Content-Length: '.filesize($file_path));
            readfile($file_path);
            exit;

        case "save":
            $content = $_POST['content'] ?? null;
            if ($content === null)
                return ['status' => 'error', 'message' => 'Content missing'];
            file_put_contents($file_path, $content);
            return ['status' => 'success', 'message' => 'File saved'];

        case "del":
            unlink($file_path);
            return ['status' => 'success', 'message' => 'File deleted'];
    }

    return ['status' => 'error', 'message' => 'Invalid action'];
}

function formatSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576)    return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)       return number_format($bytes / 1024, 2) . ' KB';
    return number_format($bytes, 2) . ' B';
}

function backupConfig() {
    while (ob_get_level()) ob_end_clean();
    $backup_name = "neko_backup_" . date("Y-m-d_H-i-s") . ".tar.gz";
    $backup_path = "/tmp/" . $backup_name;
    shell_exec("tar -czf $backup_path -C /etc/neko config proxy_provider rule_provider 2>/dev/null");

    if (!file_exists($backup_path)) die("Backup failed");

    header('Content-Type: application/gzip');
    header("Content-Disposition: attachment; filename=\"$backup_name\"");
    header('Content-Length: ' . filesize($backup_path));
    readfile($backup_path);
    unlink($backup_path);
    exit;
}

function restoreConfig() {
    if (!isset($_FILES["file_upload"])) die("No file uploaded");
    $tmp  = $_FILES["file_upload"]["tmp_name"];
    $name = basename($_FILES["file_upload"]["name"]);

    if (!preg_match('/\.tar\.gz$/', $name))
        die("Only .tar.gz allowed");

    $restore = "/tmp/" . $name;
    move_uploaded_file($tmp, $restore);

    shell_exec("rm -rf /etc/neko/config /etc/neko/proxy_provider /etc/neko/rule_provider 2>/dev/null");
    shell_exec("tar -xzf $restore -C /etc/neko 2>/dev/null");
    unlink($restore);

    shell_exec("/etc/init.d/neko restart 2>&1");

    echo json_encode(['status' => 'success', 'message' => 'Restore Successful']);
    exit;
}

if (isset($_POST["path_selector"])) {
    if ($_POST['path_selector'] === 'BACKUP CONFIG')  backupConfig();
    if ($_POST['path_selector'] === 'RESTORE CONFIG') restoreConfig();
}

if (isset($_POST["file_action"])) {
    $response = action_controller($_POST["file_action"], $allowed_dirs);
    if (!empty($response)) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    exit;
}

if (isset($_FILES["file_upload"]) && isset($_POST["upload_dir"])) {
    up_controller($_POST["upload_dir"], $allowed_dirs);
}

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'get_tables') {
        ob_clean();
        echo json_encode([
            'config' => create_table($cfg_path),
            'proxy'  => create_table($proxy_path),
            'rule'   => create_table($rule_path)
        ]);
        exit;
    }

    if ($_POST['action'] == 'get_modals') {
        ob_clean();
        echo json_encode([
            'config' => create_modal($cfg_path),
            'proxy'  => create_modal($proxy_path),
            'rule'   => create_modal($rule_path)
        ]);
        exit;
    }
}
?>
