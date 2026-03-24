'use strict';
'require view';

return view.extend({
    handleSaveApply: null,
    handleSave: null,
    handleReset: null,

    tinyFmUrls: [
        '/tinyfm/tinyfm.php?p=etc%2Fneko',
        '/tinyfm/TinyFM.php?p=etc%2Fneko',
        '/tinyfm/index.php?p=etc%2Fneko',
        '/tinyfilemanager/tinyfilemanager.php?p=etc%2Fneko',
        '/tinyfilemanager/index.php?p=etc%2Fneko'
    ],

    async load() {
        for (const url of this.tinyFmUrls) {
            try {
                const res = await fetch(url + '&_=' + Date.now(), {
                    method: 'GET',
                    cache: 'no-store',
                    credentials: 'same-origin'
                });
                res.body?.cancel?.();
                if (res.ok) return url;
            } catch (_) {}
        }
        return null;
    },

    render(iframePath) {
        if (!iframePath) {
            const msg = E('div', {
                style: 'padding:20px;border:1px solid #ccc;border-radius:8px;color:#c00;line-height:1.6;'
            });

            msg.appendChild(document.createTextNode(
                _('TinyFileManager not found. Please install luci-app-tinyfm. ')
            ));

            msg.appendChild(E('a', {
                href: 'https://github.com/Yogxx/tinyfm-snapshot',
                target: '_blank',
                rel: 'noopener noreferrer',
                style: 'margin-left:4px;font-weight:bold;'
            }, _('Download package')));

            return E('div', { class: 'cbi-section' }, msg);
        }

        const wrapper = E('div', { class: 'cbi-section' });
        const loading = E('div', { style: 'padding:20px;color:#888;' },
            _('Loading TinyFileManager...')
        );
        const iframe = E('iframe', {
            src: iframePath + '&_=' + Date.now(),
            style: 'width:100%;height:80vh;border:none;display:block;'
        });

        let failed = false;

        const showError = () => {
            if (failed) return;
            failed = true;
            if (loading.parentNode) loading.remove();
            if (iframe.parentNode)  iframe.remove();
            wrapper.appendChild(E('div', {
                style: 'color:red;padding:20px;border:1px solid #ccc;border-radius:8px;'
            }, _('Failed to load TinyFileManager.')));
        };

        iframe.addEventListener('load', () => {
            if (loading.parentNode) loading.remove();
            try {
                const doc = iframe.contentDocument;
                if (doc && doc.body && !doc.body.innerText.trim())
                    showError();
            } catch (_) {}
        });

        iframe.addEventListener('error', showError);

        wrapper.appendChild(loading);
        wrapper.appendChild(iframe);

        return wrapper;
    }
});
