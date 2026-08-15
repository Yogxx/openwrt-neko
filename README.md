<h1 align="center">
  <img src="https://raw.githubusercontent.com/Yogxx/openwrt-neko/refs/heads/main/img/neko.png" alt="neko" width="500">
</h1>

<p align="center">
  A Mihomo/Sing-box based transparent proxy client for OpenWrt with a full LuCI web interface.<br>
  Supports XRAY/V2Ray, Shadowsocks, ShadowsocksR, and more.
</p>

<div align="center">
  <a target="_blank" href="https://github.com/Yogxx/openwrt-neko/releases">
    <img src="https://img.shields.io/github/downloads/Yogxx/openwrt-neko/total?label=Total%20Download&labelColor=blue&style=for-the-badge">
  </a>
  <a target="_blank" href="https://github.com/Yogxx/openwrt-neko/releases">
    <img src="https://img.shields.io/github/v/release/Yogxx/openwrt-neko?style=for-the-badge&label=Latest%20Release">
  </a>
  <a target="_blank" href="https://dbai.team/discord">
    <img src="https://img.shields.io/discord/1127928183824597032?style=for-the-badge&logo=discord&label=Discord">
  </a>
</div>

---

## Features

- Transparent proxy modes: Redirect / TPROXY / TUN
- Dual-core support — switch between **Mihomo** or **Sing-box**
- Backup & restore configuration
- Manage configs, proxies, and rules directly from the web UI
- Built-in Xray/V2Ray subscription/config converter

## Prerequisites

- OpenWrt **23.05** or later
- `firewall4`

## Installation

### 1. Download packages

Download `mihomo` and [`luci-app-neko`](https://github.com/Yogxx/openwrt-neko/releases/tag/neko-dev) from the releases page. Also download `sing-box` if you plan to use the Sing-box core.

### 2. Install dependencies

**apk (OpenWrt 23.05+ / SNAPSHOT):**
```bash
apk update
apk add kmod-tun kmod-nft-tproxy kmod-nft-nat ip-full php8 php8-cgi
```

**opkg (OpenWrt 23.05 stable):**
```bash
opkg update
opkg install kmod-tun kmod-nft-tproxy kmod-nft-nat ip-full php8 php8-cgi
```

### 3. Install the package

**opkg:**
```bash
opkg install luci-app-neko*.ipk
```

**apk:**
```bash
apk add --allow-untrusted luci-app-neko*.apk
```

## Screenshots

<details>
<summary>Home — Mihomo</summary>
<p>
  <img src="https://raw.githubusercontent.com/Yogxx/openwrt-neko/refs/heads/main/img/mihomo.png" alt="Mihomo home screen">
</p>
</details>

<details>
<summary>Home — Sing-Box</summary>
<p>
  <img src="https://raw.githubusercontent.com/Yogxx/openwrt-neko/refs/heads/main/img/sing-box.png" alt="Sing-box home screen">
</p>
</details>

<details>
<summary>Settings</summary>
<p>
  <img src="https://raw.githubusercontent.com/Yogxx/openwrt-neko/refs/heads/main/img/settings.png" alt="Settings screen">
</p>
</details>

## About

This project is a fork of the original **luci-app-neko** by [nosignals](https://github.com/nosignals). Since the upstream project is no longer actively maintained, this fork continues development and updates independently.

## Credits

- [nosignals](https://github.com/nosignals) — original author
- [bobbyunknown](https://github.com/bobbyunknown)
