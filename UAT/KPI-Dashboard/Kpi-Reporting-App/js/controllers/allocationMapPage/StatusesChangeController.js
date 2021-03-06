kpiReporting.controller('StatusesChangeController', function ($scope, $location, $routeParams, testCasesData) {

    if (!$scope.authentication.isLoggedIn()) {
        $location.path('/login');
        return;
    }

    $scope.prepareStatuses = function (tcId, tcStatusId) {
        $scope.mapData.statusChanges[tcId] = $scope.mapData.statuses.filter(function (status) {
            return status.id == tcStatusId;
        })[0];
    };

    $scope.changeTestCaseStatus = function (tc) {
        var isNewStatusPending = $scope.mapData.statusChanges[tc.testCaseId].name == 'Pending';
        var userId = $scope.authentication.getUserData().id;

        if (isNewStatusPending) {
            kpiReporting.noty.error('Cannot change status to \'Pending\'');
        } else {
            var data = {
                userId: userId,
                testCaseId: tc.testCaseId,
                oldStatusId: tc.statusId,
                newStatus: $scope.mapData.statusChanges[tc.testCaseId]
            };

            testCasesData.changeTestCaseStatus($routeParams['id'], data).then($scope.onChangeStatusSuccess, $scope.functions.onError);
        }
    };

    $scope.onChangeStatusSuccess = function () {
        $scope.testCase.statusId = $scope.mapData.statusChanges[$scope.testCase.testCaseId].id;
        $scope.testCase.statusName = $scope.mapData.statusChanges[$scope.testCase.testCaseId].name;
        $scope.testCase.isFinal = $scope.testCase.statusId == 2 ? 1 : $scope.testCase.statusId == 4 ? 1 : 0;

        kpiReporting.noty.success("Test case status changed to: " + $scope.testCase.statusName);
        $scope.mapData.statusChanges[$scope.testCase.testCaseId] = false;
    };
});