kpiReporting.controller('DateChangeController', function ($scope, $location, $routeParams, testCasesData) {

    if (!$scope.authentication.isLoggedIn()) {
        $location.path('/login');
        return;
    }

    $scope.prepareDays = function (tcId, dayIndex) {
        $scope.data.daysChanges[tcId] = $scope.data.remainingDays.filter(function (day) {
            return day.dayIndex == dayIndex;
        })[0];
    };

    $scope.changeTestCaseDate = function (tc) {
        var data = {
            testCaseId: tc.testCaseId,
            oldDayId: tc.dayId,
            newDayId: $scope.data.daysChanges[tc.testCaseId].dayId,
            reasonId: 1
        };
        testCasesData.changeTestCaseDate($routeParams['id'], data).then($scope.onDateChangeSuccess, $scope.data.onError);
    };

    $scope.onDateChangeSuccess = function () {
        $scope.testCase.dayIndex = $scope.data.daysChanges[$scope.testCase.testCaseId].dayIndex;
        $scope.testCase.dayDate = $scope.data.daysChanges[$scope.testCase.testCaseId].dayDate;
        $scope.testCase.dayPreview = $scope.data.daysChanges[$scope.testCase.testCaseId].dayPreview;

        var currentDate = new Date();
        var testCaseDate = new Date($scope.testCase.dayDate);

        $scope.testCase.canEdit = currentDate <= testCaseDate ? 1 : 0;
        $scope.data.project.testCases.sort(function (day1, day2) {
            return day1.dayIndex - day2.dayIndex;
        });

        $scope.data.daysChanges[$scope.testCase.testCaseId] = false;
        kpiReporting.noty.success("Test case date changed to: " + $scope.testCase.dayDate);
    }
});