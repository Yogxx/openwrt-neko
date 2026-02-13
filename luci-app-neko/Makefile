include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.0-beta
PKG_RELEASE:=1

# Biarkan kosong jika tidak ada source code yang perlu dicompile (hanya skrip)
PKG_BUILD_DIR:=$(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/$(PKG_NAME)
	SECTION:=luci
	CATEGORY:=LuCI
	SUBMENU:=3. Applications
	TITLE:=LuCI support for Neko Mihomo
	PKGARCH:=all
	# Menambahkan dependensi yang lebih aman dan mendukung apk-tools
	DEPENDS:=+luci-base +kmod-tun +bash +jq +php8 +php8-cgi +mihomo
endef

define Package/$(PKG_NAME)/description
	A LuCI support for Mihomo (TUN based proxy) with PHP interface.
endef

# Karena kita tidak mengompile C/C++, kita cukup copy file ke build dir
define Build/Prepare
	mkdir -p $(PKG_BUILD_DIR)
	$(CP) ./* $(PKG_BUILD_DIR)/
endef

define Build/Compile
	# Tidak ada proses kompilasi
endef

define Package/$(PKG_NAME)/install
	# Folder Konfigurasi Neko
	$(INSTALL_DIR) $(1)/etc/neko
	if [ -d $(PKG_BUILD_DIR)/root/etc/neko ]; then \
		$(CP) $(PKG_BUILD_DIR)/root/etc/neko/* $(1)/etc/neko/ ; \
	fi

	# Scripts & Services
	$(INSTALL_DIR) $(1)/etc/init.d
	if [ -f $(PKG_BUILD_DIR)/root/etc/init.d/neko ]; then \
		$(INSTALL_BIN) $(PKG_BUILD_DIR)/root/etc/init.d/neko $(1)/etc/init.d/neko ; \
	fi

	$(INSTALL_DIR) $(1)/etc/uci-defaults
	if [ -f $(PKG_BUILD_DIR)/root/etc/uci-defaults/99_neko ]; then \
		$(INSTALL_BIN) $(PKG_BUILD_DIR)/root/etc/uci-defaults/99_neko $(1)/etc/uci-defaults/99_neko ; \
	fi

	# Web Interface (PHP)
	$(INSTALL_DIR) $(1)/www/nekoclash
	if [ -d $(PKG_BUILD_DIR)/htdocs ]; then \
		$(CP) $(PKG_BUILD_DIR)/htdocs/* $(1)/www/nekoclash/ ; \
	fi
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
