kpiReporting.noty = (function () {
    return {
        error: function (text) {
            $('.noty-container').noty({text: text, timeout: 4000, type: 'error', maxVisible: 10, layout: 'top'});
        },
        success: function (text) {
            $('.noty-container').noty({text: text, timeout: 4000, type: 'success', maxVisible: 10, layout: 'top'});
        },
        warn: function (text) {
            $('.noty-container').noty({text: text, timeout: 4000, type: 'warning', maxVisible: 10, layout: 'top'});
        },
        getPermanentWarning: function (text) {
            return $('.noty-container').noty({text: text, closeWith: [], type: 'warning', maxVisible: 25, layout: 'top'});
        },
        closeAll: function () {
            $.noty.closeAll();
        }
    };
})();