kpiReporting.controller('UserChangeController', function ($scope, $location, testCasesData) {

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
        testCasesData.changeTestCaseUser(data).then(onChangeSuccess, onChangeError);
    };

    function onChangeSuccess() {
        var tc = $scope.testCase;
        $scope.testCase.userId = $scope.data.userChanges[tc.testCaseId].id;
        $scope.testCase.username = $scope.data.userChanges[tc.testCaseId].username;

        $scope.data.userChanges[$scope.testCase.testCaseId] = false;
        kpiReporting.noty.success("Test case user changed to: " + $scope.testCase.username);
    }

    function onChangeError(error) {
        kpiReporting.noty.error(error.status + ': ' + error.data);
    }
});