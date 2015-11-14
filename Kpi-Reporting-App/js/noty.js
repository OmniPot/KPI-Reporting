kpiReporting.noty = (function () {
	var notifications = {
		error: function (text) {
			$('#notyContainer').noty({ text: text, timeout: 4000, type: 'error', maxVisible: 2, layout:'top' });
		},
		success: function (text) {
			$('#notyContainer').noty({ text: text, timeout: 2000, type: 'success', maxVisible: 2, layout:'top'  });
		},
		warn: function (text) {
			$('#notyContainer').noty({ text: text, timeout: 3000, type: 'warning', maxVisible: 2, layout:'top'  });
		}
	}

	return notifications;
})();