<?php
/**
 * version.php
 * Safely get installed versions of NekoClash, Mihomo, and Singbox
 */

header('Content-Type: application/json');

// -----------------------------
// 1. NekoClash (luci-app-neko)
// -----------------------------
#$neko_version = exec("apk list | grep luci-app-neko | cut -d ' - ' -f3");
#$neko_latest = exec("curl -m 5 -f -s https://raw.githubusercontent.com/Yogxx/openwrt-neko/main/luci-app-neko/Makefile | grep PKG_VERSION: | cut -d= -f2");
$neko_version = exec("apk list --installed luci-app-neko | awk '{print $1}' | cut -d'-' -f4-");
$neko_latest = exec("curl -m 5 -fsSL https://raw.githubusercontent.com/Yogxx/openwrt-neko/main/luci-app-neko/Makefile | awk -F':=' '/PKG_VERSION/ {print $2}'");


// -----------------------------
// 2. Mihomo
// -----------------------------
#$mihomo_version = exec("opkg list-installed | grep mihomo | cut -d ' - ' -f3");
$mihomo_version = exec("apk list --installed mihomo | awk '{print $1}'");
if(empty($mihomo_version)) $mihomo_version = "Not Installed";

// -----------------------------
// 3. Singbox
// -----------------------------
#$singbox_version = exec("opkg list-installed | grep sing-box | cut -d ' - ' -f3");
$singbox_version = exec("apk list --installed sing-box | awk '{print $1}'");
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
