# See /LICENSE for more information.
# This is free software, licensed under the GNU General Public License v3.

include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.1
PKG_RELEASE:=2
PKG_LICENSE:=MIT
PKG_MAINTAINER:=Yogxx <https://github.com/Yogxx/openwrt-neko>

LUCI_TITLE:=LuCI support for Mihomo (Neko)
LUCI_DEPENDS:=+kmod-tun
LUCI_PKGARCH:=all

include $(TOPDIR)/feeds/luci/luci.mk

define Package/$(PKG_NAME)/description
 LuCI support for Mihomo core (Neko).
endef

define Package/$(PKG_NAME)/install
	$(INSTALL_DIR) $(1)

	# Install htdocs → /www
	if [ -d $(CURDIR)/htdocs ]; then \
		$(INSTALL_DIR) $(1)/www; \
		$(CP) $(CURDIR)/htdocs/* $(1)/www/; \
	fi

	# Install root → /
	if [ -d $(CURDIR)/root ]; then \
		$(CP) $(CURDIR)/root/* $(1)/; \
	fi
endef

define Package/$(PKG_NAME)/postrm
#!/bin/sh
rm -rf /etc/neko 2>/dev/null || true
rm -rf /www/luci-static/resources/view/neko 2>/dev/null || true
rm -rf /www/nekoclash 2>/dev/null || true
rm -rf /tmp/.uci/neko 2>/dev/null || true
exit 0
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
