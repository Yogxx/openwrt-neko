<h1 align="center">
  <img src="https://raw.githubusercontent.com/Yogxx/openwrt-neko/refs/heads/main/img/nekowok.png" alt="neko" width="500">
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
| luci-app-neko | ` 1.3.0-beta ` | <div align="center"> [all-arch](https://github.com/Yogxx/openwrt-neko/releases/download/neko-dev/luci-app-neko_1.3.0-beta_all.ipk) </div> | Include `config simple rules` files |
| mihomo | ` 1.19.17 ` | <div align="center"> [x86](https://github.com/Yogxx/openwrt-neko/releases/tag/core_1.19.17_1.12.12)</br>[aarch64-generic](https://github.com/Yogxx/openwrt-neko/releases/tag/core_1.19.17_1.12.12) </div> | Latest stable version on [MetaCubeX](https://github.com/MetaCubeX/mihomo/releases) |
| sing-box | ` 1.12.12 ` | <div align="center"> [sing-box core](https://github.com/Yogxx/openwrt-neko/releases/tag/core_1.19.17_1.12.12) </div> | Original [Repository](https://github.com/SagerNet/sing-box/releases) |

Manual Installation
---
1. Download
[mihomo](https://github.com/Yogxx/openwrt-neko/releases/tag/core_1.19.17_1.12.12) & [luci-app-neko](https://github.com/Yogxx/openwrt-neko/releases/tag/neko-dev) install ` sing-box ` if needed
3. install requirement depedencies
```bash
opkg update && opkg install php8 php8-cgi kmod-tun bash curl jq ip-full ca-bundle
```
5. upload to root <br>
-` mihomo.ipk `<br>
-` luci-app-neko.ipk `<br>
-` sing-box.ipk ` <br>
- make sure there are only those 3 files
6. run command <br>
` cd ` <br>
` opkg install *.ipk `
7. Done, check your LUCI on openwrt


Updating
---
- `Rausah`

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
