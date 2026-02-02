include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.0-beta
PKG_RELEASE:=1

PKG_BUILD_DIR:=$(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/$(PKG_NAME)
	CATEGORY:=LuCI
	SUBMENU:=3. Applications
	TITLE:=LuCI support for mihomo
	PKGARCH:=all
	DEPENDS:=+luci-base \
		+kmod-tun \
		+bash \
		+curl-nossl \
		+jq \
		+mihomo \
		+sing-box \
		+php8 \
		+php8-cgi \
		+php8-mod-curl
endef

define Package/$(PKG_NAME)/description
	A LuCI support for mihomo
endef

define Build/Prepare
	$(Build/Prepare/Default)
endef

define Build/Compile
endef

define Package/$(PKG_NAME)/install
	# etc/neko (optional)
	if [ -d ./root/etc/neko ]; then \
		$(INSTALL_DIR) $(1)/etc/neko; \
		$(CP) ./root/etc/neko/* $(1)/etc/neko/ 2>/dev/null || true; \
	fi

	# init.d
	if [ -f ./root/etc/init.d/neko ]; then \
		$(INSTALL_DIR) $(1)/etc/init.d; \
		$(INSTALL_BIN) ./root/etc/init.d/neko $(1)/etc/init.d/neko; \
	fi

	# uci-defaults
	if [ -f ./root/etc/uci-defaults/99_neko ]; then \
		$(INSTALL_DIR) $(1)/etc/uci-defaults; \
		$(INSTALL_BIN) ./root/etc/uci-defaults/99_neko \
			$(1)/etc/uci-defaults/99_neko; \
	fi

	# htdocs (optional)
	if [ -d ./htdocs ]; then \
		$(INSTALL_DIR) $(1)/www/nekoclash; \
		$(CP) ./htdocs/* $(1)/www/nekoclash/ 2>/dev/null || true; \
	fi
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
