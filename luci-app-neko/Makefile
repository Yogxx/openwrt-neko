# See /LICENSE for more information.
# This is free software, licensed under the GNU General Public License v3.

include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.0
PKG_RELEASE:=5
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

	if [ -d $(CURDIR)/www ]; then \
		$(INSTALL_DIR) $(1)/www; \
		$(CP) $(CURDIR)/www/* $(1)/www/; \
	fi
endef

define Package/$(PKG_NAME)/prerm
#!/bin/sh
if ubus list service 2>/dev/null | grep -q '"neko"'; then
    ubus call service delete '{"name":"neko"}' >/dev/null 2>&1
fi
exit 0
endef

define Package/$(PKG_NAME)/postrm
#!/bin/sh

rm -rf /etc/neko 2>/dev/null || true
rm -rf /etc/config/neko 2>/dev/null || true
rm -rf /tmp/.uci/neko 2>/dev/null || true

exit 0
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
