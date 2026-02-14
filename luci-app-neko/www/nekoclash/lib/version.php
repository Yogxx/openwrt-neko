<?php
/**
 * version.php
 * Safely get installed versions of NekoClash, Mihomo, and Singbox
 */

header('Content-Type: application/json');

// -----------------------------
// 1. NekoClash (luci-app-neko)
// -----------------------------
$neko_version = exec("opkg list-installed | grep luci-app-neko | cut -d ' - ' -f3");
$neko_latest = exec("curl -m 5 -f -s https://raw.githubusercontent.com/nosignals/openwrt-neko/main/luci-app-neko/Makefile | grep PKG_VERSION: | cut -d= -f2");

// -----------------------------
// 2. Mihomo
// -----------------------------
$mihomo_version = exec("opkg list-installed | grep mihomo | cut -d ' - ' -f3");
if(empty($mihomo_version)) $mihomo_version = "Not Installed";

// -----------------------------
// 3. Singbox
// -----------------------------
$singbox_version = exec("opkg list-installed | grep sing-box | cut -d ' - ' -f3");
if(empty($singbox_version)) $singbox_version = "Not Installed";

// -----------------------------
// Response JSON
// -----------------------------
$response = [
    'neko' => [
        'current' => trim($neko_version),
        'latest'  => trim($neko_latest),
        'needsUpdate' => (trim($neko_version) != trim($neko_latest))
    ],
    'mihomo' => trim($mihomo_version),
    'singbox' => trim($singbox_version)
];

echo json_encode($response);
