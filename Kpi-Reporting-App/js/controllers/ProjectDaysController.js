kpiReporting.controller('ProjectDaysController',
    function ($scope, $location, $routeParams, daysData, projectsData, durationTolerance) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.functions.clearRedirectParams();
            $scope.functions.redirectToProjectDaysAllocation = $routeParams['id'];
            $location.path('/login');
            return;
        }

        var planRenewMsg = 'Tolerable commitment change exceeded! Plan will be renewed if saved.';

        $scope.warnings = {};
        $scope.data.loaded = false;
        $scope.daysData = {
            alerts: [],
            extensionReasons: [],
            planRenew: 0,
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
                    $scope.daysData.reasons = result.data;
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
                var tolerance = Math.round(day.expectedTestCases * (10 / 100));
                var delta = Math.abs(day.expectedTestCases - day.allocatedTestCases);

                if (delta > tolerance) {
                    $scope.daysData.alerts[day.dayId] = true;
                } else {
                    $scope.daysData.alerts[day.dayId] = false;
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
        $scope.checkForPlanRenew = function () {
            var tolerance = Math.round($scope.daysData.allocatedDays.length * (durationTolerance / 100));
            var extension = $scope.daysData.extensionDuration;

            if (extension > tolerance && !$scope.warnings.planRenew) {
                $scope.warnings.planRenew = kpiReporting.noty.getPermanentError(planRenewMsg);
                $scope.daysData.planRenew = 1;
            } else if (extension <= tolerance && $scope.warnings.planRenew) {
                $scope.warnings.planRenew.close();
                $scope.warnings.planRenew = false;
                $scope.daysData.planRenew = 0;
            }
        };

        $scope.overrideConfiguration = function () {
            daysData.overrideConfiguration($routeParams['id']).then(
                onConfigurationOverrideSuccess, $scope.functions.onError);
        };
        $scope.extendPlan = function (daysCount) {
            $scope.data.loaded = false;

            var lastDay = $scope.daysData.allocatedDays[$scope.daysData.allocatedDays.length - 1];
            var firstExtendedDay = $scope.functions.addDays(new Date(lastDay.dayDate), 1);
            var expectedTestCases = parseInt(lastDay.expectedTestCases);

            var data = {
                startDate: $scope.functions.formatDate(firstExtendedDay),
                startDuration: $scope.daysData.allocatedDays.length,
                endDuration: $scope.daysData.allocatedDays.length + daysCount,
                expectedTestCases: expectedTestCases,
                algorithm: $scope.daysData.renewAlgorithm,
                extensionReasons: $scope.daysData.extensionReasons,
                planRenew: $scope.daysData.planRenew
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
            $scope.daysData.planRenew = 0;
            $scope.daysData.planResetAccept = false;
            $scope.daysData.renewAlgorithm = 1;

            $scope.checkForPlanRenew();
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