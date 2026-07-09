'use strict';
'require view';

return view.extend({
    render: function() {
        return E('iframe', {
            src: window.location.origin + '/nekoclash/configs.php',
            style: 'width: 100%; min-height: 95vh; border: none; border-radius: 5px; resize: vertical;'
        });
    },
    handleSaveApply: null,
    handleSave: null,
    handleReset: null
});
