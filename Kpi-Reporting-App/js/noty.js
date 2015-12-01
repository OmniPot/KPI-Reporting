kpiReporting.noty = (function () {
    return {
        error: function (text) {
            $('.notyContainer').noty({text: text, timeout: 4000, type: 'error', maxVisible: 2, layout: 'top'});
        },
        success: function (text) {
            $('.notyContainer').noty({text: text, timeout: 2000, type: 'success', maxVisible: 2, layout: 'top'});
        },
        warn: function (text) {
            $('.notyContainer').noty({text: text, timeout: 3000, type: 'warning', maxVisible: 2, layout: 'top'});
        },
        permanentError: function (text) {
            $('.notyContainer').noty({text: text, closeWith: ['button'], type: 'error', maxVisible: 5, layout: 'top'});
        },
        permanentWarning: function (text) {
            $('.notyContainer').noty({text: text, closeWith: ['button'], type: 'warning', maxVisible: 5, layout: 'top'});
        }, closeAll: function () {
            $.noty.closeAll();
        },
        getWarning: function (text) {
            return $('.notyContainer').noty({text: text, closeWith: ['button'], type: 'warning', maxVisible: 5, layout: 'top'});
        }
    };
})();