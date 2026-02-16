<?php
/**
 * version.php
 * Universal version checker for OpenWrt 24 (opkg) & 25 (apk)
 */

header('Content-Type: application/json');

/**
 * Detect package manager
 */
function has_apk() {
    return trim(shell_exec("which apk 2>/dev/null")) !== "";
}

function has_opkg() {
    return trim(shell_exec("which opkg 2>/dev/null")) !== "";
}

/**
 * Get installed version
 */
function get_version($pkg) {
    if (has_apk()) {
        // OpenWrt 25+
        $cmd = "apk list --installed $pkg 2>/dev/null | awk '{print \$1}' | sed 's/^$pkg-//'";
    } elseif (has_opkg()) {
        // OpenWrt 24
        $cmd = "opkg list-installed 2>/dev/null | grep ^$pkg' ' | awk '{print \$3}'";
    } else {
        return "Unknown";
    }

    $version = trim(shell_exec($cmd));
    return $version !== "" ? $version : "Not Installed";
}

/**
 * Get latest Neko version from GitHub
 */
function get_latest_neko() {
    $cmd = "curl -m 5 -fsSL https://raw.githubusercontent.com/Yogxx/openwrt-neko/main/luci-app-neko/Makefile 2>/dev/null | awk -F':=' '/PKG_VERSION/ {print \$2}'";
    return trim(shell_exec($cmd));
}

/**
 * Get versions
 */
$neko_version    = get_version("luci-app-neko");
$mihomo_version  = get_version("mihomo");
$singbox_version = get_version("sing-box");

$neko_latest = get_latest_neko();

/**
 * Build response
 */
$response = [
    'neko' => [
        'current'     => $neko_version,
        'latest'      => $neko_latest,
        'needsUpdate' => ($neko_version !== "Not Installed" && $neko_latest !== "" && $neko_version != $neko_latest)
    ],
    'mihomo'  => $mihomo_version,
    'singbox' => $singbox_version
];

echo json_encode($response);
