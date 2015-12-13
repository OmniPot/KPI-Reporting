kpiReporting.controller('UserChangeController', function ($scope, $location, $routeParams, testCasesData) {

    if (!$scope.authentication.isLoggedIn()) {
        $location.path('/login');
        return;
    }

    $scope.prepareUsersForAllocated = function (tcId, userId) {
        $scope.data.userChanges[tcId] = $scope.data.users.filter(function (user) {
            return user.id == userId;
        })[0];
    };

    $scope.prepareUsersForUnallocated = function (tcId) {
        $scope.data.userChanges[tcId] = $scope.data.users[0];
    };

    $scope.changeTestCaseUser = function (tc) {
        var data = {
            testCaseId: tc.testCaseId,
            oldUserId: tc.userId,
            newUserId: $scope.data.userChanges[tc.testCaseId].id,
            externalStatus: 1
        };

        if (tc.dayIndex) {
            data.externalStatus = 2;
        }

        testCasesData.changeTestCaseUser($routeParams['id'], data).then($scope.onUserChangeSuccess, $scope.functions.onError);
    };

    $scope.onUserChangeSuccess = function () {
        $scope.testCase.userId = $scope.data.userChanges[$scope.testCase.testCaseId].id;
        $scope.testCase.username = $scope.data.userChanges[$scope.testCase.testCaseId].username;

        if ($scope.testCase.dayIndex) {
            $scope.testCase.externalStatus = 2;
            $scope.data.unallocated = $scope.data.unallocated > 0 ? $scope.data.unallocated - 1 : 0;

            $scope.testCase.canEdit = $scope.functions.resolveCanEdit($scope.testCase);

            $scope.data.testCases.sort(function (day1, day2) {
                return day1.dayIndex - day2.dayIndex;
            });
        }

        $scope.data.userChanges[$scope.testCase.testCaseId] = false;
        kpiReporting.noty.success("Test case user changed to: " + $scope.testCase.username);
    };
});