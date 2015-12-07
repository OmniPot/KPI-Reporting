kpiReporting.controller('ProjectDaysController', function ($scope, $location, $routeParams, daysData, projectsData) {

    // Authenticate
    if (!$scope.authentication.isLoggedIn()) {
        $scope.functions.clearRedirectParams();
        $scope.functions.redirectToProjectDaysAllocation = $routeParams['id'];
        $location.path('/login');
        return;
    }

    $scope.daysData = {
        changes: [],
        alerts: []
    };

    $scope.getProjectDetails = function () {
        projectsData.getProjectDetails($routeParams['id']).then(
            function success(result) {
                $scope.data.project = result.data;

                $scope.getProjectAllocatedDays();
            },$scope.functions.onError
        )
    };

    $scope.getProjectAllocatedDays = function () {
        daysData.getProjectAllocatedDays($routeParams['id']).then(
            function success(result) {
                if (result.data.config == false) {
                    $location.path('/projects/' + $routeParams['id'] + '/setup');
                    return;
                }

                $scope.daysData.config = result.data.config;
                $scope.daysData.activeUsers = result.data.activeUsers;
                $scope.daysData.allocatedDays = result.data.allocatedDays;

                $scope.calculateDeltas();

            },$scope.functions.onError
        )
    };

    $scope.calculateDeltas = function () {
        $scope.daysData.allocatedDays.forEach(function (day) {
            var tolerance = Math.round((day.expectedTestCases * 10 / 100) * 10) / 10;
            var delta = Math.abs(day.expectedTestCases - day.allocatedTestCases);

            if (delta > tolerance) {
                $scope.daysData.alerts[day.dayId] = true;
            }
        });
    };

    $scope.getProjectDetails();
});