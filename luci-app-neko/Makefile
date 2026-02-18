include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.0
PKG_RELEASE:=4

include $(INCLUDE_DIR)/package.mk

define Package/$(PKG_NAME)
	CATEGORY:=LuCI
	SUBMENU:=3. Applications
	TITLE:=LuCI support for Mihomo
	PKGARCH:=all
	DEPENDS:=+luci-base +kmod-tun
endef

define Package/$(PKG_NAME)/description
LuCI support for Mihomo core.
endef

define Package/$(PKG_NAME)/install
	$(INSTALL_DIR) $(1)

	if [ -d ./root ]; then \
		$(CP) ./root/* $(1)/; \
	fi

	if [ -d ./www ]; then \
		$(INSTALL_DIR) $(1)/www; \
		$(CP) ./www/* $(1)/www/; \
	fi
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
