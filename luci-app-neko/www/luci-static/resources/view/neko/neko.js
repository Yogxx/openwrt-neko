'use strict';
'require view';
'require uci';
'require ui';
'require fs';
'require form';
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
        
        var xhr = new Promise(function(resolve, reject) {
            fs.exec('/etc/init.d/neko', [action])
                .then(resolve)
                .catch(reject);
            
            setTimeout(function() {
                resolve({ code: 0 });
            }, 5000);
        });
        
        return xhr.then(function(res) {
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
                ui.addNotification(null, E('p', _('Service ' + action + ' failed: ' + res.stderr)), 'error');
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
                var notification = JSON.parse(savedNotification);
                ui.addNotification(null, E('p', _(notification.message)), notification.type);
                localStorage.removeItem('neko_notification');
            }
            return data;
        });
    },

    render: function(data) {
        let show_luci = uci.get('neko', 'cfg', 'show_luci');
        let core_mode = uci.get('neko', 'cfg', 'core_mode');
        
        let isRunning = false;
        let serviceData = data[1];
        
        if (core_mode === 'mihomo') {
            if (serviceData.neko && serviceData.neko.instances && serviceData.neko.instances.instance1) {
                isRunning = serviceData.neko.instances.instance1.running;
            }
        } else if (core_mode === 'singbox') {
            if (serviceData.neko && serviceData.neko.instances) {
                // Cek instance2
                if (serviceData.neko.instances.instance2) {
                    isRunning = serviceData.neko.instances.instance2.running;
                } else {
                    for (let instance in serviceData.neko.instances) {
                        if (serviceData.neko.instances[instance].running) {
                            isRunning = true;
                            break;
                        }
                    }
                }
            }
        }

        if (show_luci === '1') {
            return E('iframe', {
                src: window.location.protocol + "//" + window.location.hostname + '/nekoclash',
                style: 'width: 100%; min-height: 95vh; border: none; border-radius: 5px; resize: vertical;'
            });
        }

        if (show_luci === '0') {
            return E('div', { 'class': 'cbi-map' }, [
                E('style', {}, `
                    .loading-spinner {
                        display: inline-block;
                        width: 16px;
                        height: 16px;
                        border: 2px solid rgba(255,255,255,.3);
                        border-radius: 50%;
                        border-top-color: #fff;
                        animation: spin 1s ease-in-out infinite;
                        margin: 0 auto;
                    }

                    @keyframes spin {
                        to { transform: rotate(360deg); }
                    }
                `),
                E('h2', {}, [ _('𝙽𝚎𝚔𝚘𝙲𝚕𝚊𝚜𝚑') ]),
                E('div', { 'class': 'cbi-map-descr', 'style': 'margin-bottom: 10px;' }, [ 
                    E('span', { 'style': 'font-weight: bold; font-size: 20px;' }, [ _('Mihomo/Singbox Core') ]),
                    E('div', { 'style': 'margin-top: 10px;padding-left: 10px;' }, [
                        E('span', {'style': 'font-weight: bold;'}, [ core_mode.toUpperCase() + ': ' ]),
                        E('span', { 'style': isRunning ? 'color:green' : 'color:red' },
                            [ (isRunning ? _('Running') : _('Stopped')) ])
                    ])
                ]),
                E('div', { 'class': 'cbi-section' }, [
                    E('div', { 'class': 'cbi-section-node' }, [
                        E('div', { 'class': 'cbi-value-field' }, [
                            isRunning ? E('button', {
                                'class': 'cbi-button cbi-button-negative',
                                'click': L.bind(function() {
                                    return this.handleServiceAction('stop');
                                }, this)
                            }, [ _('Stop') ]) : E('button', {
                                'class': 'cbi-button cbi-button-apply',
                                'click': L.bind(function() {
                                    return this.handleServiceAction('start');
                                }, this)
                            }, [ _('Start') ]),
                            ' ',
                            E('button', {
                                'class': 'cbi-button cbi-button-action',
                                'style': isRunning ? '' : 'display:none',
                                'click': L.bind(function() {
                                    return this.handleServiceAction('restart');
                                }, this)
                            }, [ _('Restart') ]),
                            ' ',
                            E('button', {
                                'class': 'cbi-button cbi-button-apply',
                                'click': function() {
                                    window.open('/nekoclash', '_blank', 'noopener');
                                }
                            }, [ _('Open Neko') ])
                        ])
                    ])
                ])
            ]);
        }

        return m.render();
    },

    handleSaveApply: null,
    handleSave: null,
    handleReset: null
});
