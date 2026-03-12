'use strict';
'require view';
'require uci';
'require ui';
'require fs';
'require rpc';

var callServiceList = rpc.declare({
    object: 'service',
    method: 'list',
    params: ['name'],
    expect: { '': {} }
});

return view.extend({

    handleServiceAction: function(action) {
        var btn = document.activeElement;
        var originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<div class="loading-spinner"></div>';

        return fs.exec('/etc/init.d/neko', [action])
            .then(function(res) {
                if (res.code === 0) {
                    setTimeout(function() {
                        localStorage.setItem('neko_notification', JSON.stringify({
                            type: 'success',
                            message: 'Service ' + action + ' success'
                        }));
                        window.location.reload();
                    }, 2000);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    ui.addNotification(null, E('p', _('Service ' + action + ' failed: ' + (res.stderr || ''))), 'error');
                }
            })
            .catch(function(err) {
                btn.disabled = false;
                btn.innerHTML = originalContent;
                ui.addNotification(null, E('p', _('Service ' + action + ' failed: ' + err)), 'error');
            });
    },

    load: function() {
        return Promise.all([
            uci.load('neko'),
            callServiceList('neko')
        ]).then(function(data) {
            var savedNotification = localStorage.getItem('neko_notification');
            if (savedNotification) {
                try {
                    var notification = JSON.parse(savedNotification);
                    ui.addNotification(null, E('p', _(notification.message)), notification.type);
                } catch(_) {}
                localStorage.removeItem('neko_notification');
            }
            return data;
        });
    },

    render: function(data) {
        var show_luci = uci.get('neko', 'cfg', 'show_luci') || '0';
        var core_mode = uci.get('neko', 'cfg', 'core_mode') || 'mihomo';

        var isRunning   = false;
        var serviceData = data[1];

        // FIX: loop semua instance — tidak hardcode instance1/instance2
        if (serviceData && serviceData.neko && serviceData.neko.instances) {
            var instances = serviceData.neko.instances;
            for (var key in instances) {
                if (instances[key].running) {
                    isRunning = true;
                    break;
                }
            }
        }

        if (show_luci === '1') {
            return E('iframe', {
                src: window.location.origin + '/nekoclash',
                style: 'width: 100%; min-height: 95vh; border: none; border-radius: 5px; resize: vertical;'
            });
        }

        return E('div', { 'class': 'cbi-map' }, [
            E('style', {}, [
                '.loading-spinner {',
                '    display: inline-block;',
                '    width: 16px;',
                '    height: 16px;',
                '    border: 2px solid rgba(255,255,255,.3);',
                '    border-radius: 50%;',
                '    border-top-color: #fff;',
                '    animation: spin 1s ease-in-out infinite;',
                '    margin: 0 auto;',
                '}',
                '@keyframes spin { to { transform: rotate(360deg); } }'
            ].join('\n')),

            E('h2', {}, [_('𝙽𝚎𝚔𝚘𝙲𝚕𝚊𝚜𝚑')]),

            E('div', { 'class': 'cbi-map-descr', 'style': 'margin-bottom: 10px;' }, [
                E('span', { 'style': 'font-weight: bold; font-size: 20px;' }, [_('Mihomo/Singbox Core')]),
                E('div', { 'style': 'margin-top: 10px; padding-left: 10px;' }, [
                    E('span', { 'style': 'font-weight: bold;' }, [core_mode.toUpperCase() + ': ']),
                    E('span', { 'style': isRunning ? 'color:green' : 'color:red' },
                        [isRunning ? _('Running') : _('Stopped')])
                ])
            ]),

            E('div', { 'class': 'cbi-section' }, [
                E('div', { 'class': 'cbi-section-node' }, [
                    E('div', { 'class': 'cbi-value-field' }, [
                        isRunning
                            ? E('button', {
                                'class': 'cbi-button cbi-button-negative',
                                'click': L.bind(function() {
                                    return this.handleServiceAction('stop');
                                }, this)
                            }, [_('Stop')])
                            : E('button', {
                                'class': 'cbi-button cbi-button-apply',
                                'click': L.bind(function() {
                                    return this.handleServiceAction('start');
                                }, this)
                            }, [_('Start')]),
                        ' ',
                        E('button', {
                            'class': 'cbi-button cbi-button-action',
                            'style': isRunning ? '' : 'display:none',
                            'click': L.bind(function() {
                                return this.handleServiceAction('restart');
                            }, this)
                        }, [_('Restart')]),
                        ' ',
                        E('button', {
                            'class': 'cbi-button cbi-button-apply',
                            'click': function() {
                                window.open(window.location.origin + '/nekoclash', '_blank', 'noopener');
                            }
                        }, [_('Open Neko')])
                    ])
                ])
            ])
        ]);
    },

    handleSaveApply: null,
    handleSave: null,
    handleReset: null
});
