# See /LICENSE for more information.
# This is free software, licensed under the GNU General Public License v3.
# Copyright (C) 2024 nosignal

include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.0
PKG_RELEASE:=5
PKG_LICENSE:=MIT
PKG_MAINTAINER:=nosignal <https://github.com/nosignals/openwrt-neko>

LUCI_TITLE:=LuCI support for Mihomo
LUCI_DEPENDS:=+kmod-tun +bash +curl +php8 +php8-cgi
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

	if [ -d $(CURDIR)/www ]; then \
		$(INSTALL_DIR) $(1)/www; \
		$(CP) $(CURDIR)/www/* $(1)/www/; \
	fi
endef

define Package/$(PKG_NAME)/conffiles
/etc/config/neko
endef

define Package/$(PKG_NAME)/postrm
#!/bin/sh
if command -v ubus >/dev/null 2>&1; then
    ubus call service delete '{"name":"neko"}' 2>/dev/null || true
fi

[ -f /etc/init.d/neko ] && rm -f /etc/init.d/neko
[ -d /etc/neko ] && rm -rf /etc/neko
[ -f /etc/config/neko ] && rm -f /etc/config/neko
[ -d /www/luci-static/resources/view/neko ] && rm -rf /www/luci-static/resources/view/neko
[ -d /www/nekoclash ] && rm -rf /www/nekoclash
[ -d /tmp/.uci/neko ] && rm -rf /tmp/.uci/neko

exit 0
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
