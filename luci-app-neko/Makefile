# See /LICENSE for more information.
# Licensed under GPLv3
# Copyright (C) 2024 nosignal

include $(TOPDIR)/rules.mk

PKG_NAME:=luci-app-neko
PKG_VERSION:=1.3.0
PKG_RELEASE:=1
PKG_MAINTAINER:=nosignal <https://github.com/nosignals/neko>

PKG_BUILD_DIR:=$(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/$(PKG_NAME)
	CATEGORY:=LuCI
	SUBMENU:=3. Applications
	TITLE:=LuCI support for Mihomo
	PKGARCH:=all
	DEPENDS:=+kmod-tun +bash +curl +jq +mihomo +php8 +php8-mod-curl
endef

define Package/$(PKG_NAME)/description
LuCI support for Mihomo core.
endef


# =========================
# Build
# =========================

define Build/Prepare
	mkdir -p $(PKG_BUILD_DIR)
	$(CP) ./root $(PKG_BUILD_DIR)/
	$(CP) ./www $(PKG_BUILD_DIR)/

	chmod 0755 $(PKG_BUILD_DIR)/root/etc/init.d/neko 2>/dev/null || true
	chmod -R 0755 $(PKG_BUILD_DIR)/root/etc/neko/core 2>/dev/null || true
	chmod 0755 $(PKG_BUILD_DIR)/root/etc/uci-defaults/99_neko 2>/dev/null || true

	# Inject version
	sed -i "s/0.0.0-beta/$(PKG_VERSION)/g" \
	$(PKG_BUILD_DIR)/root/etc/neko/core/neko 2>/dev/null || true
endef

define Build/Configure
endef

define Build/Compile
endef


# =========================
# Install
# =========================

define Package/$(PKG_NAME)/install
	$(INSTALL_DIR) $(1)/

	# Install system files
	[ -d $(PKG_BUILD_DIR)/root ] && \
	$(CP) $(PKG_BUILD_DIR)/root/* $(1)/ || true

	# Install web files (LuCI JS view + optional web ui)
	$(INSTALL_DIR) $(1)/www
	[ -d $(PKG_BUILD_DIR)/www ] && \
	$(CP) $(PKG_BUILD_DIR)/www/* $(1)/www/ || true
endef


$(eval $(call BuildPackage,$(PKG_NAME)))
