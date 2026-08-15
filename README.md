<h1 align="center">
  <img src="https://raw.githubusercontent.com/Yogxx/openwrt-neko/refs/heads/main/img/neko.png" alt="neko" width="500">
</h1>

<div align="center">
 <a target="_blank" href="https://github.com/Yogxx/openwrt-neko/releases"><img src="https://img.shields.io/github/downloads/Yogxx/openwrt-neko/total?label=Total%20Download&labelColor=blue&style=for-the-badge"></a>
 <a target="_blank" href="https://dbai.team/discord"><img src="https://img.shields.io/discord/1127928183824597032?style=for-the-badge&logo=discord&label=%20"></a>
</div>


<p align="center">
  XRAY/V2ray, Shadowsocks, ShadowsocksR, etc.</br>
  Mihomo based Proxy
</p>

Features
---
- Transparent Proxy (Redirect/TPROXY/TUN)
- Dual mode, can use mihomo or singbox core
- Backup & Restore configuration
- Configs, Proxy, and Rules can edit on webui
- Xray/V2ray config converter

Prerequisites
---
- firewall4
- OpenWrt >= 23.05

Manual Installation
---
1. Download ` mihomo ` & [luci-app-neko](https://github.com/Yogxx/openwrt-neko/releases/tag/neko-dev) install ` sing-box ` if needed
3. install requirement depedencies
```bash
apk update && apk add kmod-tun kmod-nft-tproxy kmod-nft-nat ip-full php8 php8-cgi
```
```bash
opkg update && opkg install kmod-tun kmod-nft-tproxy kmod-nft-nat ip-full php8 php8-cgi
```
```
# for opkg
opkg install luci-app-neko*.ipk
# for apk
apk add --allow-untrusted luci-app-neko*.apk
```

About
---
nosignal is gone

Credit
---
- [nosignals](https://github.com/nosignals)
- [bobbyunknown](https://github.com/bobbyunknown)

Screenshoot
---
<details><summary>Home - Mihomo</summary>
 <p>
  <img src="https://raw.githubusercontent.com/nosignals/openwrt-neko/refs/heads/dev/img/mihomo.png" alt="home">
 </p>
</details>

<details><summary>Home - Sing-Box</summary>
  <img src="https://raw.githubusercontent.com/nosignals/openwrt-neko/refs/heads/dev/img/sing-box.png" alt="cfg">
</details>

<details><summary>Settings</summary>
  <img src="https://raw.githubusercontent.com/Yogxx/openwrt-neko/refs/heads/main/img/settings.png" alt="setting">
</details>
