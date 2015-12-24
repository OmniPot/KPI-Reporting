kpiReporting.controller('ProjectDaysController',
    function ($scope, $location, $routeParams, daysData, projectsData) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.functions.clearRedirectParams();
            $scope.data.redirectToProjectDays = $routeParams['id'];
            $location.path('/login');
            return;
        }

        $scope.data.loaded = false;
        $scope.daysData = {
            alerts: [],
            deleteOptions: [],
            extensionReasons: []
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
                    $scope.prepareDeleteOptions(result.data.allocatedDays);
                    $scope.data.loaded = true;
                }, $scope.functions.onError
            )
        };

        $scope.getAvailableDays = function (day) {
            daysData.getAvailableDays($routeParams['id']).then(
                function success(result) {
                    day.availableDates = result.data;
                }, $scope.functions.onError
            );
        };
        $scope.changeDayDate = function (day) {
            var data = {
                newDate: day.dateToChange
            };

            daysData.changeDayDate(day.dayId, data).then(
                function success(result) {
                    kpiReporting.noty.success(result.data.msg);
                    $scope.getProjectConfig();
                }, $scope.functions.onError
            )
        };

        $scope.prepareDeleteOptions = function (days) {
            for (var i = days.length - 1; i >= 0; i--) {
                if (days[i].period == 3 && days[i].allocated == 0) {
                    $scope.daysData.deleteOptions[days[i].dayId] = true;
                } else {
                    break;
                }
            }
        };
        $scope.calculateDeltas = function () {
            $scope.daysData.alerts = [];
            $scope.daysData.allocatedDays.forEach(function (day) {
                var expected = day.expected - day.blocked;
                var tolerance = Math.round(expected * (10 / 100));
                var delta = expected - day.executed;

                if (day.period == 1 || day.period == 2) {
                    if (delta > tolerance) {
                        $scope.daysData.alerts[day.dayId] = 'color:#FF6600;font-size:1.25em;';
                    } else if (delta >= -tolerance && delta <= tolerance) {
                        $scope.daysData.alerts[day.dayId] = false;
                    } else if (delta < -tolerance) {
                        $scope.daysData.alerts[day.dayId] = 'color:#66CD00;font-size:1.25em;';
                    }
                } else {
                    delta = Math.abs(expected - day.allocated);
                    if (delta > tolerance) {
                        $scope.daysData.alerts[day.dayId] = 'color:#FF6600;font-size:1.25em;';
                    } else {
                        $scope.daysData.alerts[day.dayId] = false;
                    }
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
        $scope.deleteDay = function (day) {
            daysData.deleteDay($routeParams['id'], day.dayId).then(
                function success(result) {
                    kpiReporting.noty.success(result.data.msg);
                    $scope.getProjectConfig();
                }, $scope.functions.onError
            )
        };

        $scope.stopExecution = function () {
            var data = {
                reason: $scope.daysData.selectedParkReason
            };

            daysData.stopProjectExecution($routeParams['id'], data).then(
                onStopResumeSuccess, $scope.functions.onError
            )
        };
        $scope.resumeExecution = function () {
            daysData.resumeProjectExecution($routeParams['id']).then(
                onStopResumeSuccess, $scope.functions.onError)
        };

        function onStopResumeSuccess(result) {
            kpiReporting.noty.success(result.data.msg);
            $scope.getProjectConfig();
        }

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
                $scope.getExtensionReasons();
                $scope.getParkReasons();
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
                if (day.period == 2 || day.period == 3) {
                    day.expected = day.allocated;
                }
            });

            $scope.calculateDeltas();
        }

        $scope.getProjectConfig();
    });