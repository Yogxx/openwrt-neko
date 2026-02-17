<?php

header('Content-Type: application/json');

function has_apk() {
    return trim(shell_exec("which apk 2>/dev/null")) !== "";
}

function has_opkg() {
    return trim(shell_exec("which opkg 2>/dev/null")) !== "";
}

function get_version($pkg) {
    if (has_apk()) {

        $cmd = "apk list --installed $pkg 2>/dev/null | awk '{print \$1}' | sed 's/^$pkg-//'";
    } elseif (has_opkg()) {

        $cmd = "opkg list-installed 2>/dev/null | grep ^$pkg' ' | awk '{print \$3}'";
    } else {
        return "Unknown";
    }

    $version = trim(shell_exec($cmd));
    return $version !== "" ? $version : "Not Installed";
}

function get_latest_neko() {
    $cmd = "curl -m 5 -fsSL https://raw.githubusercontent.com/Yogxx/openwrt-neko/main/luci-app-neko/Makefile 2>/dev/null | awk -F':=' '/PKG_VERSION/ {print \$2}'";
    return trim(shell_exec($cmd));
}

$neko_version    = get_version("luci-app-neko");
$mihomo_version  = get_version("mihomo");
$singbox_version = get_version("sing-box");

$neko_latest = get_latest_neko();

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
