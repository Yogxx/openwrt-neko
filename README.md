<h1 align="center">
  <img src="https://raw.githubusercontent.com/nosignals/neko/main/img/neko.png" alt="neko" width="500">
</h1>

<div align="center">
 <a target="_blank" href="https://github.com/nosignals/neko/releases"><img src="https://img.shields.io/github/downloads/nosignals/neko/total?label=Total%20Download&labelColor=blue&style=for-the-badge"></a>
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

Packages list
---
| Packages | Version | Arch | Information |
|---|---|---|---|
| [luci-app-neko](https://github.com/yogxx/openwrt-neko/tree/main/luci-app-neko) | ` 1.3.0-beta ` | <div align="center"> [all-generic](https://github.com/yogxx/openwrt-neko/releases/download/luci-app-neko_1.3.0-beta/luci-app-neko_1.3.0-beta_all.ipk) </div> | Include `geosite` and `geoip` files |
| [mihomo](https://github.com/yogxx/openwrt-neko/tree/main/mihomo) | ` 1.19.17 ` | <div align="center"> [x86](https://github.com/yogxx/openwrt-neko/releases/download/mihomo_1.19.17/mihomo_1.19.17_x86_64.ipk)</br>[aarch64-generic](https://github.com/yogxx/openwrt-neko/releases/download/mihomo_1.19.17/mihomo_1.19.17_aarch64_generic.ipk)</br>[arm_cortex-a7_neon](https://github.com/yogxx/openwrt-neko/releases/download/mihomo_1.19.17/mihomo_1.19.17_arm_cortex-a7_neon-vfpv4.ipk) </div> | Latest stable version on [MetaCubeXD](https://github.com/MetaCubeX/mihomo/) |
| [sing-box](https://github.com/yogxx/openwrt-neko/tree/main/sing-box) | ` 1.12.12 ` | - | [Original Repository](https://github.com/SagerNet/sing-box) |

Installation
---
1. Downloads ` mihomo_xxxx.ipk ` and ` luci-app-neko_xxx.ipk ` from releases
2. install requirement depedencies `php8 php8-cgi kmod-tun bash curl jq ip-full ca-bundle`
3. Install firstly ` mihomo_xxxx.ipk ` and ` sing-box_xxxx.ipk `
4. After installing ` mihomo ` and ` sing-box `, install ` luci-app-neko_xxx.ipk `
5. Done, check your LUCI on openwrt

Updating
---
1. Download updated `mihomo` or `luci-app-neko`
2. Remove old version `opkg remove luci-app-neko`
3. Installing downloaded update ` opkg install luci-app-neko_xxx.ipk `
4. Done, check your LUCI on openwrt

Compiling
---
#### 1. Add feeds
```bash
echo "src-git neko https://github.com/nosignals/openwrt-neko.git;main" >> "feeds.conf.default"
```
#### 2. Update & Install feeds
```bash
./scripts/feeds update -a
./scripts/feeds install -a
```
#### 3. Make Packages
```bash
make package/luci-app-neko/compile
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
  <img src="https://raw.githubusercontent.com/nosignals/openwrt-neko/refs/heads/dev/img/setting.png" alt="setting">
</details>
