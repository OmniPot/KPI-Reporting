kpiReporting.controller('UserChangeController', function ($scope, $location, $routeParams, testCasesData) {

    if (!$scope.authentication.isLoggedIn()) {
        $location.path('/login');
        return;
    }

    $scope.prepareUsersForAllocated = function (tcId, userId) {
        $scope.mapData.userChanges[tcId] = $scope.mapData.users.filter(function (user) {
            return user.id == userId;
        })[0];
    };

    $scope.prepareUsersForUnallocated = function (tcId) {
        $scope.mapData.userChanges[tcId] = $scope.mapData.users[0];
    };

    $scope.changeTestCaseUser = function (tc) {
        var data = {
            testCaseId: tc.testCaseId,
            oldUserId: tc.userId,
            newUserId: $scope.mapData.userChanges[tc.testCaseId].id,
            externalStatus: 1
        };

        if (tc.dayIndex) {
            data.externalStatus = 2;
        }

        testCasesData.changeTestCaseUser($routeParams['id'], data).then($scope.onUserChangeSuccess, $scope.functions.onError);
    };

    $scope.onUserChangeSuccess = function () {
        $scope.testCase.userId = $scope.mapData.userChanges[$scope.testCase.testCaseId].id;
        $scope.testCase.username = $scope.mapData.userChanges[$scope.testCase.testCaseId].username;

        if ($scope.testCase.dayIndex) {
            $scope.testCase.externalStatus = 2;
            $scope.mapData.unallocated = $scope.mapData.unallocated > 0 ? $scope.mapData.unallocated - 1 : 0;

            $scope.testCase.canEdit = $scope.functions.resolveCanEdit($scope.testCase);

            $scope.mapData.testCases.sort(function (day1, day2) {
                return day1.dayIndex - day2.dayIndex;
            });
        }

        $scope.mapData.userChanges[$scope.testCase.testCaseId] = false;
        kpiReporting.noty.success("Test case user changed to: " + $scope.testCase.username);
    };
});