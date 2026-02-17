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
	DEPENDS:=+kmod-tun +bash +curl +jq +php8 +php8-mod-curl
endef

define Package/$(PKG_NAME)/description
LuCI support for Mihomo core.
endef

define Build/Prepare
	mkdir -p $(PKG_BUILD_DIR)
	$(CP) ./root $(PKG_BUILD_DIR)/
	$(CP) ./www $(PKG_BUILD_DIR)/
	chmod 0755 $(PKG_BUILD_DIR)/root/etc/init.d/neko 2>/dev/null || true
	chmod 0755 $(PKG_BUILD_DIR)/root/etc/uci-defaults/99_neko 2>/dev/null || true
endef

define Package/$(PKG_NAME)/install
	$(INSTALL_DIR) $(1)
	$(CP) $(PKG_BUILD_DIR)/root/* $(1)/ || true
	$(INSTALL_DIR) $(1)/www
	$(CP) $(PKG_BUILD_DIR)/www/* $(1)/www/ || true
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
