kpiReporting.controller('ProjectSetupController',
    function ($scope, $location, $routeParams, projectsData, usersData, setupData) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $location.path('/login');
            return;
        }

        var planRenewMsg = 'Tolerable commitment change exceeded! Plan will be renewed if saved.';
        var durationMismatch = 'Suggested and actual duration mismatch.';
        var overPerformMsg = 'OVERPERFORMING.';
        var underPerformMsg = 'UNDERPERFORMING.';

        $scope.warnings = {
            duration: null,
            tcpd: null
        };
        $scope.setupData = {
            project: {},
            activeUsers: [],
            durationTolerance: 5,
            TCPDTolerance: 10,
            planRenew: 0,
            algorithm: 1,
            suggestedDuration: 0,
            acceptableSuggestedDurationDelta: 0
        };

        $scope.getProjectDetails = function () {
            projectsData.getProjectDetails($routeParams['id']).then(
                function success(result) {
                    $scope.setupData.project = result.data;
                    $scope.setupData.nonFinalTestCasesCount = parseInt(result.data.nonFinalTestCasesCount);

                    $scope.onDurationChange();
                }, $scope.functions.onError
            );
        };
        $scope.getAllUsers = function () {
            usersData.getAllUsers().then(
                function success(result) {
                    $scope.setupData.allUsers = result.data;
                }, $scope.functions.onError
            );
        };
        $scope.suggestUsers = function (users) {
            users.forEach(function (user) {
                Object.keys(user).forEach(function (key) {
                    user[key] = parseInt(user[key]);
                });
                $scope.setupData.activeUsers[user.userId] = {
                    id: parseInt(user.userId),
                    load: user.loadIndicator,
                    performance: user.performanceIndicator,
                    index: user.performanceIndex
                };
            });
        };

        $scope.onUserSelect = function (user) {
            if ($scope.setupData.activeUsers[user.id] == undefined) {
                $scope.setupData.activeUsers[user.id] = {id: parseInt(user.id), load: 100, performance: 0};
                $scope.onUserTCPDChange(user.id, user.performanceIndex);
            } else {
                delete $scope.setupData.activeUsers[user.id];
            }

            $scope.onDurationChange();
        };
        $scope.onUserTCPDChange = function (userId, index) {
            var load = $scope.setupData.activeUsers[userId].load;
            $scope.setupData.activeUsers[userId].performance = Math.round((index / 100) * load);

            $scope.calculateExpectedTCPD();
            $scope.calculateActualTCPD();
            $scope.durationMismatchCalculations();
            $scope.overUnderPerformCalculations();
        };
        $scope.onDurationChange = function () {
            $scope.calculateActualTCPD();
            $scope.calculateExpectedTCPD();

            $scope.planRenewCalculations();
            $scope.durationMismatchCalculations();
            $scope.overUnderPerformCalculations();
        };

        $scope.calculateExpectedTCPD = function () {
            $scope.setupData.expectedTCPD = Math.round($scope.setupData.activeUsers.reduce(function (a, b) {
                return a + b.performance;
            }, 0));
        };
        $scope.calculateActualTCPD = function () {
            $scope.setupData.actualTCPD = Math.round($scope.setupData.nonFinalTestCasesCount / $scope.setupData.duration);
            $scope.setupData.acceptableTCPDDelta = Math.round($scope.setupData.expectedTCPD *
                ($scope.setupData.TCPDTolerance / 100));
        };

        $scope.planRenewCalculations = function () {
            $scope.setupData.acceptablePraviousDurationDelta =
                $scope.setupData.previousDuration * ($scope.setupData.durationTolerance / 100);

            var delta = $scope.setupData.duration - $scope.setupData.previousDuration;
            var roundedDelta = Math.round(Math.abs(delta) * 10) / 10;

            if (roundedDelta > $scope.setupData.acceptablePraviousDurationDelta && $scope.setupData.existingPlan && !$scope.warnings.planRenew) {
                $scope.warnings.planRenew = kpiReporting.noty.getPermanentError(planRenewMsg);
                $scope.setupData.planRenew = 1;
            } else if (roundedDelta <= $scope.setupData.acceptablePraviousDurationDelta && $scope.warnings.planRenew) {
                $scope.warnings.planRenew.close();
                $scope.warnings.planRenew = false;
                $scope.setupData.planRenew = 0;
            }
        };
        $scope.durationMismatchCalculations = function () {
            if ($scope.setupData.nonFinalTestCasesCount > 0 && $scope.setupData.expectedTCPD > 0) {
                var suggestedDuration = $scope.setupData.nonFinalTestCasesCount / $scope.setupData.expectedTCPD;
                var suggestedDurationRounded = Math.round(suggestedDuration * 10) / 10;
                var acceptableSuggestedDurationDelta = suggestedDurationRounded * ($scope.setupData.durationTolerance / 100);

                $scope.setupData.suggestedDuration = suggestedDurationRounded;
                $scope.setupData.acceptableSuggestedDurationDelta = Math.round(acceptableSuggestedDurationDelta * 10) / 10;

                var delta = Math.abs(suggestedDurationRounded - $scope.setupData.duration);
                var suggestedDelta = Math.round(delta * 10) / 10;

                if (suggestedDelta > $scope.setupData.acceptableSuggestedDurationDelta && !$scope.warnings.duration) {
                    $scope.warnings.duration = kpiReporting.noty.getPermanentWarning(durationMismatch);
                } else if (suggestedDelta <= $scope.setupData.acceptableSuggestedDurationDelta && $scope.warnings.duration) {
                    $scope.warnings.duration.close();
                    $scope.warnings.duration = false;
                }
            }
        };
        $scope.overUnderPerformCalculations = function () {

            var TCPDDelta = $scope.setupData.expectedTCPD - $scope.setupData.actualTCPD;
            var absTCPDDelta = Math.abs(TCPDDelta);

            if (absTCPDDelta > $scope.setupData.acceptableTCPDDelta && TCPDDelta > 0) {
                if (!$scope.warnings.underperform) {
                    $scope.warnings.underperform = kpiReporting.noty.getPermanentWarning(underPerformMsg);
                }
                if ($scope.warnings.overperform) {
                    $scope.warnings.overperform.close();
                    $scope.warnings.overperform = false;
                }
            }
            if (absTCPDDelta > $scope.setupData.acceptableTCPDDelta && TCPDDelta < 0) {
                if (!$scope.warnings.overperform) {
                    $scope.warnings.overperform = kpiReporting.noty.getPermanentWarning(overPerformMsg);
                }
                if ($scope.warnings.underperform) {
                    $scope.warnings.underperform.close();
                    $scope.warnings.underperform = false;
                }
            }
            if (absTCPDDelta <= $scope.setupData.acceptableTCPDDelta) {
                if ($scope.warnings.underperform) {
                    $scope.warnings.underperform.close();
                    $scope.warnings.underperform = false;
                }
                if ($scope.warnings.overperform) {
                    $scope.warnings.overperform.close();
                    $scope.warnings.overperform = false;
                }
            }
        };

        $scope.saveSetup = function () {
            kpiReporting.noty.warn('Processing configuration...');
            var data = {
                activeUsers: $scope.setupData.activeUsers,
                duration: $scope.setupData.duration,
                algorithm: parseInt($scope.setupData.algorithm),
                nonFinalTestCasesCount: $scope.setupData.nonFinalTestCasesCount,
                expectedTCPD: $scope.setupData.expectedTCPD,
                actualTCPD: $scope.setupData.actualTCPD,
                planRenew: $scope.setupData.planRenew
            };

            setupData.saveSetup($scope.setupData.project.id, data).then(
                function success() {
                    kpiReporting.noty.success('Successfully saved configuration!');
                    $location.path('projects/' + $routeParams['id'] + '/allocationMap');
                }, $scope.functions.onError
            );
        };

        setupData.getSetupDetails($routeParams['id']).then(
            function success(result) {
                if (result.data.activeUsers.length == 0 && result.data.durations.currentDuration == '0') {
                    $scope.setupData.existingPlan = false;
                    $scope.setupData.duration = parseFloat(result.data.durations.wrikeDuration);
                    $scope.setupData.previousDuration = $scope.setupData.duration;
                } else {
                    $scope.setupData.existingPlan = true;
                    $scope.setupData.duration = parseFloat(result.data.durations.currentDuration);
                    $scope.setupData.previousDuration = $scope.setupData.duration;
                }

                $scope.getProjectDetails();
                $scope.getAllUsers();
                $scope.suggestUsers(result.data.activeUsers);
                $scope.calculateExpectedTCPD();
            }, $scope.functions.onError
        );
    });