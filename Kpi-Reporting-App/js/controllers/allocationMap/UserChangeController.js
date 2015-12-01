kpiReporting.controller('UserChangeController', function ($scope, $location, $routeParams, testCasesData) {

    if (!$scope.authentication.isLoggedIn()) {
        $location.path('/login');
        return;
    }

    $scope.prepareUsers = function (tcId, userId) {
        $scope.data.userChanges[tcId] = $scope.data.users.filter(function (user) {
            return user.id == userId;
        })[0];
    };

    $scope.changeTestCaseUser = function (tc) {
        var data = {
            testCaseId: tc.testCaseId,
            oldUserId: tc.userId,
            newUserId: $scope.data.userChanges[tc.testCaseId].id
        };
        testCasesData.changeTestCaseUser($routeParams['id'], data).then($scope.onUserChangeSuccess, $scope.data.onError);
    };

    $scope.onUserChangeSuccess = function () {
        var tc = $scope.testCase;
        $scope.testCase.userId = $scope.data.userChanges[tc.testCaseId].id;
        $scope.testCase.username = $scope.data.userChanges[tc.testCaseId].username;

        $scope.data.userChanges[$scope.testCase.testCaseId] = false;
        kpiReporting.noty.success("Test case user changed to: " + $scope.testCase.username);
    };
});