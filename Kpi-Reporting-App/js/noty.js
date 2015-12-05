kpiReporting.noty = (function () {
    return {
        error: function (text) {
            $('.notyContainer').noty({text: text, timeout: 4000, type: 'error', maxVisible: 10, layout: 'top'});
        },
        success: function (text) {
            $('.notyContainer').noty({text: text, timeout: 2000, type: 'success', maxVisible: 10, layout: 'top'});
        },
        warn: function (text) {
            $('.notyContainer').noty({text: text, timeout: 3000, type: 'warning', maxVisible: 10, layout: 'top'});
        },
        getPermanentWarning: function (text) {
            return $('.notyContainer').noty({text: text, closeWith: [], type: 'warning', maxVisible: 25, layout: 'top'});
        },
        getPermanentError: function (text) {
            return $('.notyContainer').noty({text: text, closeWith: [], type: 'error', maxVisible: 18, layout: 'top'});
        },
        closeAll: function () {
            $.noty.closeAll();
        }
    };
})();