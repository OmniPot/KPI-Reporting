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
        extensionReasons: []
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

    $scope.addReason = function (reason) {
        if (!reason) {
            kpiReporting.noty.warn('Choose a reason first.');
        } else {
            var existing = $scope.daysData.extensionReasons.filter(function (r) {
                return r.id == reason.id
            })[0];

            if (existing) {
                kpiReporting.noty.warn('Cannot add a reason multiple times.');
            } else {
                $scope.daysData.extensionReasons.push(reason);
            }
        }
    };
    $scope.removeReason = function (reason) {
        $scope.daysData.extensionReasons = $scope.daysData.extensionReasons.filter(function (r) {
            return r.id != reason.id;
        });
    };

    $scope.extendPlan = function (daysCount) {
        var lastDay = $scope.daysData.allocatedDays[$scope.daysData.allocatedDays.length - 1];
        var lastDayDate = new Date(lastDay.dayDate);
        var firstExtendedDay = $scope.functions.addDays(lastDayDate, 1);

        var data = {
            startDate: $scope.functions.formatDate(firstExtendedDay),
            startDuration: $scope.daysData.allocatedDays.length,
            endDuration: $scope.daysData.allocatedDays.length + daysCount,
            expectedTestCases: lastDay.expectedTestCases,
            extensionReasons: $scope.daysData.extensionReasons
        };

        daysData.extendProjectDuration($routeParams['id'], data).then(
            function success(result) {
                kpiReporting.noty.success('Project extended successfully!');
                $scope.daysData.allocatedDays = result.data;
                $scope.calculateDeltas();
            }, $scope.functions.onError
        );
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

    $scope.getProjectConfig();
});