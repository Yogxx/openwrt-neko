<?php
header('Content-Type: application/json');

function has_apk() {
    return trim(shell_exec("which apk 2>/dev/null")) !== "";
}

function has_opkg() {
    return trim(shell_exec("which opkg 2>/dev/null")) !== "";
}

function get_version($pkg) {
    $pkg = preg_replace('/[^a-zA-Z0-9\-]/', '', $pkg);
    if ($pkg === "") return "Unknown";

    if (has_apk()) {
        $cmd = "apk list --installed " . escapeshellarg($pkg) . " 2>/dev/null | awk '{print \$1}' | sed 's/^" . $pkg . "-//'";
    } elseif (has_opkg()) {
        $cmd = "opkg list-installed 2>/dev/null | grep " . escapeshellarg("^$pkg ") . " | awk '{print \$3}'";
    } else {
        return "Unknown";
    }

    $version = trim(shell_exec($cmd));
    return $version !== "" ? $version : "Not Installed";
}

function get_latest_neko() {
    $url = "https://raw.githubusercontent.com/Yogxx/openwrt-neko/main/luci-app-neko/Makefile";
    $escaped_url = escapeshellarg($url);
    $content = trim(shell_exec("curl -m 5 -fsSL $escaped_url 2>/dev/null"));

    if ($content === "") return "";

    $escaped_content = escapeshellarg($content);
    $pkg_version = trim(shell_exec("echo $escaped_content | awk -F':=' '/^PKG_VERSION/ {print \$2}'"));
    $pkg_release  = trim(shell_exec("echo $escaped_content | awk -F':=' '/^PKG_RELEASE/ {print \$2}'"));

    if ($pkg_version === "") return "";
    return $pkg_release !== "" ? $pkg_version . "-r" . $pkg_release : $pkg_version;
}

$neko_version    = get_version("luci-app-neko");
$mihomo_version  = get_version("mihomo");
$singbox_version = get_version("sing-box");
$neko_latest     = get_latest_neko();

$needs_update = (
    $neko_version !== "Not Installed" &&
    $neko_latest  !== "" &&
    $neko_version !== $neko_latest
);

echo json_encode([
    'neko' => [
        'current'     => $neko_version,
        'latest'      => $neko_latest,
        'needsUpdate' => $needs_update
    ],
    'mihomo'  => $mihomo_version,
    'singbox' => $singbox_version
]);