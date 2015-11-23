kpiReporting.controller('StatusesController', function ($scope, executionsData) {

    $scope.data.executable = [];

    $scope.prepareStatuses = function (tcId, tcStatusId) {
        $scope.data.executable[tcId] = $scope.data.statuses.filter(function (status) {
            return status.id == tcStatusId;
        })[0];
    };

    $scope.executeTestCase = function (tc) {
        var isNewStatusPending = $scope.data.executable[tc.testCaseId].name == 'Pending';
        //var isOldStatusChanged = $scope.data.executable[tc.testCaseId].name == tc.statusName;

        if (isNewStatusPending) {
            kpiReporting.noty.error('Cannot change status to \'Pending\'');
        } else {
            var data = {
                userId: tc.userId,
                testCaseId: tc.testCaseId,
                oldStatusId: tc.statusId,
                newStatusId: $scope.data.executable[tc.testCaseId].id
            };

            executionsData.executeTestCase(data).then(onExecutionSuccess, onExecutionError);
        }
    };

    function onExecutionSuccess() {
        var tc = $scope.testCase;
        $scope.testCase.statusId = $scope.data.executable[tc.testCaseId].id;
        $scope.testCase.statusName = $scope.data.executable[tc.testCaseId].name;
        $scope.testCase.isFinal = tc.statusId == 2 ? 1 : $scope.testCase.statusId == 4 ? 1 : 0;

        $scope.data.executable[$scope.testCase.testCaseId] = false;
        kpiReporting.noty.success("Test case status changed to: " + $scope.testCase.statusName);
    }

    function onExecutionError(error) {
        kpiReporting.noty.error(error.status + ': ' + error.data);
    }
});