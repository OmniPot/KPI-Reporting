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
        alerts: [],
        reasons: [
            {id: 1, name: 'prichina edno'},
            {id: 2, name: 'prichina dve'}
        ],
        chosenReasons: []
    };

    $scope.getProjectConfig = function () {
        projectsData.getActiveConfig($routeParams['id']).then(onGetProjectConfigSuccess, $scope.functions.onError);
    };
    $scope.getProjectById = function () {
        projectsData.getProjectById($routeParams['id']).then(onGetProjectSuccess, $scope.functions.onError
        );
    };
    $scope.getProjectAllocatedDays = function (projectId) {
        daysData.getProjectAllocatedDays(projectId).then(
            function success(result) {
                $scope.daysData.allocatedDays = result.data.allocatedDays;
                $scope.calculateDeltas();
            }, $scope.functions.onError
        )
    };

    $scope.calculateDeltas = function () {
        $scope.daysData.allocatedDays.forEach(function (day) {
            var tolerance = Math.round(day.expectedTestCases * (10 / 100));
            var delta = Math.abs(day.expectedTestCases - day.allocatedTestCases);

            if (delta > tolerance) {
                $scope.daysData.alerts[day.dayId] = true;
            }
        });
    };

    function onGetProjectConfigSuccess(result) {
        if (result.data.configId) {
            $scope.data.config = result.data;
            $scope.getProjectById($routeParams['id']);
        } else {
            kpiReporting.noty.warn('Please setup the the project with Id ' + $routeParams['id'] + ' first');
            $location.path('projects/' + $routeParams['id'] + '/setup');
        }
    }

    function onGetProjectSuccess(result) {
        if (result.data.id) {
            $scope.data.project = result.data;
            $scope.getProjectAllocatedDays($routeParams['id']);
        } else {
            $location.path('projects/' + $routeParams['id'] + '/setup');
        }
    }

    $scope.getNextWorkDay = function () {
        var lastDateObject = $scope.daysData.allocatedDays.slice(-1)[0];
        var lastDateParts = lastDateObject.dayDate.split('-');
        var lastDate = new Date(lastDateParts[0], lastDateParts[1] - 1, lastDateParts[2]);

        var nextDate = $scope.functions.addDays($scope.functions.getDateFromDatetime(lastDate), 1);
        var nextIndex = parseInt(lastDateObject.dayIndex) + 1;
        var testCases = parseInt(lastDateObject.expectedTestCases);

        $scope.daysData.dayToAdd = {
            projectId: $routeParams['id'],
            dayDate: nextDate,
            dayIndex: nextIndex,
            expectedTestCases: testCases
        };

        console.log($scope.daysData.dayToAdd);
    };

    $scope.addReason = function (reason) {
        var existing = $scope.daysData.chosenReasons.filter(function (r) {
            return r.id == reason.id
        })[0];

        if (existing) {
            kpiReporting.noty.warn('aaaaaaa ne');
        } else {
            $scope.daysData.chosenReasons.push(reason);
        }
    };

    $scope.removeReason = function (reason) {
        $scope.daysData.chosenReasons = $scope.daysData.chosenReasons.filter(function (r) {
            return r.id != reason.id;
        });
    };

    $scope.getProjectConfig();
});