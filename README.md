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
- your Own Custom Theme based Bootstrap ` nekoclash/assets/theme `
- Configs, Proxy, and Rules can edit on webui
- xray/v2ray config converter

Prerequisites
---
- firewall4
- OpenWrt >= 23.05

Packages list
---
| Packages | Version | Information |
|---|---|---|
| luci-app-neko | <div align="center"> [ ` 1.3.0 ` ](https://github.com/Yogxx/openwrt-neko/releases) </div> | Include `config simple rules` files |
| mihomo | <div align="center"> [ ` 1.19.20 ` ](https://github.com/Yogxx/openwrt-neko/releases/tag/core_1.19.20_1.12.21)</div> | Latest stable version on [MetaCubeX](https://github.com/MetaCubeX/mihomo/releases) |
| sing-box | <div align="center"> [ ` 1.12.21 ` ](https://github.com/Yogxx/openwrt-neko/releases/tag/core_1.19.20_1.12.21) </div> | Original [Repository](https://github.com/SagerNet/sing-box/releases) |

Manual Installation
---
1. Download
[mihomo](https://github.com/Yogxx/openwrt-neko/releases/tag/core_1.19.20_1.12.21) & [luci-app-neko](https://github.com/Yogxx/openwrt-neko/releases/tag/neko-dev) install ` sing-box ` if needed
3. install requirement depedencies
```bash
apk update && apk add php8 php8-cgi kmod-tun bash curl jq ip-full ca-bundle
```
```bash
opkg update && opkg install php8 php8-cgi kmod-tun bash curl jq ip-full ca-bundle
```
```
# for opkg
opkg install luci-app-neko*.ipk
# for apk
apk add --allow-untrusted luci-app-neko*.apk
```
Updating
---
- `-`

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
  <img src="https://raw.githubusercontent.com/nosignals/openwrt-neko/refs/heads/dev/img/setting.png" alt="setting">
</details>
