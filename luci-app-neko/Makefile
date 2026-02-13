include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.0-beta
PKG_RELEASE:=1

PKG_BUILD_DIR:=$(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/$(PKG_NAME)
	SECTION:=luci
	CATEGORY:=LuCI
	SUBMENU:=3. Applications
	TITLE:=LuCI support for Neko Mihomo
	PKGARCH:=all
	# php8-cgi adalah standar terbaru di 25.12
	# php8-mod-openssl ditambahkan agar Neko bisa download config via HTTPS
	DEPENDS:=+luci-base +kmod-tun +bash +jq +php8 +php8-cgi +php8-mod-openssl +mihomo
endef

define Package/$(PKG_NAME)/description
	LuCI support for Mihomo. Optimized for OpenWrt 25.12 (APK) and 24.10 (IPK).
endef

define Build/Prepare
	mkdir -p $(PKG_BUILD_DIR)
	$(CP) ./* $(PKG_BUILD_DIR)/
endef

define Build/Compile
endef

define Package/$(PKG_NAME)/install
	$(INSTALL_DIR) $(1)/etc/neko
	[ -d $(PKG_BUILD_DIR)/root/etc/neko ] && $(CP) $(PKG_BUILD_DIR)/root/etc/neko/* $(1)/etc/neko/ || true

	$(INSTALL_DIR) $(1)/etc/init.d
	[ -f $(PKG_BUILD_DIR)/root/etc/init.d/neko ] && $(INSTALL_BIN) $(PKG_BUILD_DIR)/root/etc/init.d/neko $(1)/etc/init.d/neko || true

	$(INSTALL_DIR) $(1)/etc/uci-defaults
	[ -f $(PKG_BUILD_DIR)/root/etc/uci-defaults/99_neko ] && $(INSTALL_BIN) $(PKG_BUILD_DIR)/root/etc/uci-defaults/99_neko $(1)/etc/uci-defaults/99_neko || true

	$(INSTALL_DIR) $(1)/www/nekoclash
	[ -d $(PKG_BUILD_DIR)/htdocs ] && $(CP) $(PKG_BUILD_DIR)/htdocs/* $(1)/www/nekoclash/ || true
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
