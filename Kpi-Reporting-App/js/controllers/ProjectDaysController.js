kpiReporting.controller('ProjectDaysController',
    function ($scope, $location, $routeParams, daysData, projectsData, durationTolerance) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.functions.clearRedirectParams();
            $scope.functions.redirectToProjectDaysAllocation = $routeParams['id'];
            $location.path('/login');
            return;
        }

        $scope.data.loaded = false;
        $scope.daysData = {
            alerts: [],
            extensionReasons: [],
            renewAlgorithm: 1
        };

        $scope.getProjectConfig = function () {
            projectsData.getActiveConfig($routeParams['id']).then(onGetProjectConfigSuccess, $scope.functions.onError);
        };
        $scope.getProjectById = function () {
            projectsData.getProjectById($routeParams['id']).then(
                onGetProjectSuccess, $scope.functions.onError);
        };
        $scope.getExtensionReasons = function () {
            daysData.getExtensionReasons().then(
                function success(result) {
                    $scope.daysData.extReasons = result.data;
                }, $scope.functions.onError
            )
        };
        $scope.getParkReasons = function () {
            daysData.getParkReasons().then(
                function success(result) {
                    $scope.daysData.parkReasons = result.data;
                }, $scope.functions.onError
            )
        };
        $scope.getProjectAllocatedDays = function (projectId) {
            daysData.getProjectAllocatedDays(projectId).then(
                function success(result) {
                    $scope.daysData.allocatedDays = result.data.allocatedDays;
                    $scope.calculateDeltas();

                    $scope.data.loaded = true;
                }, $scope.functions.onError
            )
        };
        $scope.calculateDeltas = function () {
            $scope.daysData.allocatedDays.forEach(function (day) {
                var expected = day.expected - day.blocked;
                var tolerance = Math.round(expected * (10 / 100));
                var delta = Math.abs(expected - day.executed);

                if (delta > tolerance) {
                    $scope.daysData.alerts[day.dayId] = true;
                } else {
                    $scope.daysData.alerts[day.dayId] = false;
                }
            });
        };
        $scope.calculateReasonsDuration = function () {
            var sum = 0;
            $scope.daysData.extensionReasons.forEach(function (reason) {
                if (!$scope.daysData.extensionReasons[reason.id].percentage) {
                    $scope.daysData.extensionReasons[reason.id].duration = 0;
                } else {
                    var float = $scope.daysData.extensionReasons[reason.id].percentage / 100;
                    var percentage = $scope.daysData.extensionDuration * float;
                    $scope.daysData.extensionReasons[reason.id].duration = Math.round(percentage * 10) / 10;
                }

                sum = sum + $scope.daysData.extensionReasons[reason.id].percentage;
            });

            $scope.daysData.canExtend = sum == 100;
        };

        $scope.addReason = function (reason) {
            if (!reason) {
                kpiReporting.noty.warn('Choose a reason first.');
            } else {
                if ($scope.daysData.extensionReasons[reason.id]) {
                    kpiReporting.noty.warn('Cannot add a reason multiple times.');
                } else {
                    $scope.daysData.extensionReasons[reason.id] = reason;
                }
            }
        };
        $scope.removeReason = function (reason) {
            delete $scope.daysData.extensionReasons[reason.id];

            $scope.calculateReasonsDuration();
        };

        $scope.overrideConfiguration = function () {
            daysData.overrideConfiguration($routeParams['id']).then(
                onConfigurationOverrideSuccess, $scope.functions.onError);
        };
        $scope.extendPlan = function (daysCount) {
            $scope.data.loaded = false;

            var data = {
                duration: daysCount,
                extensionReasons: $scope.daysData.extensionReasons
            };

            daysData.extendProjectDuration($routeParams['id'], data).then(onExtendPlanSuccess, $scope.functions.onError);
        };

        function onGetProjectConfigSuccess(result) {
            if (result.data.configId) {
                $scope.data.config = result.data;
                $scope.getProjectById($routeParams['id']);
            } else {
                kpiReporting.noty.warn('Please setup project with Id ' + $routeParams['id'] + ' first');
                $location.path('projects/' + $routeParams['id'] + '/setup');
            }
        }

        function onGetProjectSuccess(result) {
            if (result.data.id) {
                $scope.data.project = result.data;
                $scope.getExtensionReasons()
                $scope.getProjectAllocatedDays($routeParams['id']);
            } else {
                $location.path('projects/' + $routeParams['id'] + '/setup');
            }
        }

        function onExtendPlanSuccess(result) {
            kpiReporting.noty.closeAll();
            kpiReporting.noty.success(result.data.msg);

            $scope.getProjectById($routeParams['id']);

            $scope.daysData.extensionReasons = [];
            $scope.daysData.extensionDuration = '';
            $scope.daysData.selectedReason = undefined;
        }

        function onConfigurationOverrideSuccess(result) {
            kpiReporting.noty.success(result.data.msg);

            $scope.daysData.allocatedDays.forEach(function (day) {
                day.expectedTestCases = day.allocatedTestCases;
            });

            $scope.calculateDeltas();
        }

        $scope.getProjectConfig();
    });