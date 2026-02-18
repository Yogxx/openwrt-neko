include $(TOPDIR)/rules.mk

LUCI_TITLE:=LuCI support for Mihomo
LUCI_DEPENDS:=+luci-base +kmod-tun
LUCI_PKGARCH:=all

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.0
PKG_RELEASE:=4

include $(INCLUDE_DIR)/luci.mk

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

$(eval $(call BuildPackage,$(PKG_NAME)))
