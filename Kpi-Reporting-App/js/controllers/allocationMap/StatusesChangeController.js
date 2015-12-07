kpiReporting.controller('StatusesChangeController', function ($scope, $location, $routeParams, testCasesData) {

    if (!$scope.authentication.isLoggedIn()) {
        $location.path('/login');
        return;
    }

    $scope.prepareStatuses = function (tcId, tcStatusId) {
        $scope.data.statusChanges[tcId] = $scope.data.statuses.filter(function (status) {
            return status.id == tcStatusId;
        })[0];
    };

    $scope.changeTestCaseStatus = function (tc) {
        var isNewStatusPending = $scope.data.statusChanges[tc.testCaseId].name == 'Pending';
        //var isOldStatusChanged = $scope.data.statusChanges[tc.testCaseId].name == tc.statusName;

        if (isNewStatusPending) {
            kpiReporting.noty.error('Cannot change status to \'Pending\'');
        } else {
            var data = {
                userId: tc.userId,
                testCaseId: tc.testCaseId,
                oldStatusId: tc.statusId,
                newStatusId: $scope.data.statusChanges[tc.testCaseId].id
            };

            testCasesData.changeTestCaseStatus($routeParams['id'], data).then($scope.onChangeStatusSuccess,$scope.functions.onError);
        }
    };

    $scope.onChangeStatusSuccess = function () {
        $scope.testCase.statusId = $scope.data.statusChanges[$scope.testCase.testCaseId].id;
        $scope.testCase.statusName = $scope.data.statusChanges[$scope.testCase.testCaseId].name;

        $scope.testCase.isFinal = $scope.testCase.statusId == 2 ? 1 : $scope.testCase.statusId == 4 ? 1 : 0;

        $scope.data.statusChanges[$scope.testCase.testCaseId] = false;
        kpiReporting.noty.success("Test case status changed to: " + $scope.testCase.statusName);
    };
});