'use strict';
'require view';

return view.extend({
    tinyFmUrls: [
        '/tinyfilemanager/tinyfilemanager.php?p=etc%2Fneko',
        '/tinyfilemanager/index.php?p=etc%2Fneko',
        '/tinyfm/tinyfm.php?p=etc%2Fneko',
        '/tinyfm/index.php?p=etc%2Fneko'
    ],

    findValidUrl: async function() {
        for (const url of this.tinyFmUrls) {
            try {
                const cacheBuster = '&_=' + Date.now();
                const res = await fetch(url + cacheBuster, {
                    method: 'HEAD',
                    cache: 'no-store',
                    credentials: 'same-origin'
                });
                if (res.ok) return url;
            } catch (_) {}
        }
        return null;
    },

    load: function() {
        return this.findValidUrl();
    },

    render: function(iframePath) {
        if (iframePath) {
            const fullUrl = window.location.origin + iframePath;
            return this.renderIframe(fullUrl);
        }
        return E('div', { class: 'cbi-section' }, [
            E('div', {
                style: 'color: red; padding: 20px; border: 1px solid #ccc; border-radius: 8px;'
            }, _('TinyFileManager not found. Please install it to use the Advanced Editor.'))
        ]);
    },

    renderIframe: function(url) {
        return E('div', { class: 'cbi-section' }, [
            E('iframe', {
                src: url,
                style: 'width: 100%; height: 80vh; border: none;',
                onerror: function(e) {
                    const iframe = e.target;
                    iframe.style.display = 'none';
                    const div = document.createElement('div');
                    div.style.cssText = 'color: red; padding: 20px;';
                    div.textContent = 'Failed to load TinyFileManager.';
                    iframe.parentNode.appendChild(div);
                }
            }, _('Your browser does not support iframes.'))
        ]);
    }
});
