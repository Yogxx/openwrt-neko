#
# Copyright (C) 2024 Nosignal <https://github.com/nosignals>
# 
# Contributors:
# - bobbyunknown <https://github.com/bobbyunknown>
#
# https://opensource.org/license/mit
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.

include $(TOPDIR)/rules.mk

PKG_NAME:=sing-box
PKG_VERSION:=1.11.10
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=https://codeload.github.com/lux5am/sing-box/tar.gz/v$(PKG_VERSION)?
PKG_HASH:=402b618148b58f5ff6c1bee4f4fdcf7cdcb88a2df6a8bd682ea742a89b5be9ec

PKG_LICENSE:=MIT
PKG_MAINTAINER:=BobbyUnknown

PKG_BUILD_DEPENDS:=golang/host
PKG_BUILD_PARALLEL:=1
PKG_BUILD_FLAGS:=no-mips16

GO_PKG:=github.com/sagernet/sing-box
GO_PKG_BUILD_PKG:=$(GO_PKG)/cmd/sing-box
GO_PKG_LDFLAGS_X:=$(GO_PKG)/constant.Version=v$(PKG_VERSION)

include $(INCLUDE_DIR)/package.mk
include $(TOPDIR)/feeds/packages/lang/golang/golang-package.mk

define Package/$(PKG_NAME)
  SECTION:=net
  CATEGORY:=Network
  TITLE:=The universal proxy platform
  URL:=https://github.com/lux5am/sing-box
  DEPENDS:=$(GO_ARCH_DEPENDS) +ca-bundle +ip-full +kmod-tun
  USERID:=sing-box=5566:sing-box=5566
endef

define Package/$(PKG_NAME)/description
  Sing-box kernel with VMess, VLESS, Shadowsocks, protocol.
endef

define Package/$(PKG_NAME)/install
	$(call GoPackage/Package/Install/Bin,$(1))
endef

$(eval $(call GoBinPackage,$(PKG_NAME)))
$(eval $(call BuildPackage,$(PKG_NAME)))
