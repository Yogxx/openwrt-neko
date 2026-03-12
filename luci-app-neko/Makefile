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

define Package/$(PKG_NAME)/description
 LuCI support for Mihomo core.
endef

define Package/$(PKG_NAME)/install
	$(INSTALL_DIR) $(1)

	if [ -d $(CURDIR)/root ]; then \
		$(CP) $(CURDIR)/root/* $(1)/; \
	fi
endef

define Package/$(PKG_NAME)/prerm
#!/bin/sh
mkdir -p /tmp/neko
touch /tmp/neko/neko.bak
[ -d /etc/neko/config ]         && cp -rf /etc/neko/config /tmp/neko/
[ -d /etc/neko/proxy_provider ] && cp -rf /etc/neko/proxy_provider /tmp/neko/
[ -d /etc/neko/rule_provider ]  && cp -rf /etc/neko/rule_provider /tmp/neko/
[ -f /etc/neko/cache.db ]       && cp -f /etc/neko/cache.db /tmp/neko/
exit 0
endef

define Package/$(PKG_NAME)/postrm
#!/bin/sh
rm -rf /etc/neko 2>/dev/null || true
rm -rf /etc/config/neko 2>/dev/null || true
rm -rf /www/luci-static/resources/view/neko 2>/dev/null || true
rm -rf /www/nekoclash 2>/dev/null || true
rm -rf /tmp/.uci/neko 2>/dev/null || true
exit 0
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
