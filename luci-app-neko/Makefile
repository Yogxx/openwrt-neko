# See /LICENSE for more information.
# This is free software, licensed under the GNU General Public License v3.

include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.1
PKG_RELEASE:=1
PKG_LICENSE:=MIT
PKG_MAINTAINER:=nosignal <https://github.com/nosignals/openwrt-neko>

LUCI_TITLE:=LuCI support for Mihomo
LUCI_DEPENDS:=+kmod-tun
LUCI_PKGARCH:=all

include $(TOPDIR)/feeds/luci/luci.mk

# Script postrm disederhanakan hanya untuk membersihkan file cache/runtime
define Package/$(PKG_NAME)/postrm
#!/bin/sh
rm -rf /etc/neko 2>/dev/null || true
rm -rf /www/nekoclash 2>/dev/null || true
rm -rf /tmp/.uci/neko 2>/dev/null || true
exit 0
endef
