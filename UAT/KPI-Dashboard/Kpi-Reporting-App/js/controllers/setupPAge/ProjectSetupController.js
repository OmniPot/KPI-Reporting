kpiReporting.controller('ProjectSetupController',
    function ($scope, $location, $routeParams, projectsData, usersData, daysData, setupData, durationTolerance, TCPDTolerance) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.functions.clearRedirectParams();
            $scope.data.redirectToProjectSetup = $routeParams['id'];
            $location.path('/login');
            return;
        }

        var durationMismatch = 'Suggested and actual duration mismatch.';
        var overPerformMsg = 'OVERPERFORMING.';
        var underPerformMsg = 'UNDERPERFORMING.';

        $scope.warnings = {
            duration: null,
            tcpd: null
        };
        $scope.data.loaded = false;
        $scope.setupData = {
            project: {},
            activeUsers: [],
            algorithm: 1,
            suggestedDuration: 0,
            acceptableSuggestedDurationDelta: 0
        };

        $scope.getProjectDetails = function () {
            setupData.getSetupDetails($routeParams['id']).then(onGetProjectDetailsSuccess, $scope.functions.onError);
        };
        $scope.getAllUsers = function () {
            usersData.getAllUsers().then(
                function success(result) {
                    $scope.setupData.allUsers = result.data;
                }, $scope.functions.onError
            );
        };
        $scope.getResetReasons = function () {
            daysData.getResetReasons().then(
                function success(result) {
                    $scope.setupData.reasons = result.data;
                }, $scope.functions.onError
            )
        };
        $scope.suggestUsers = function (users) {
            users.forEach(function (user) {
                Object.keys(user).forEach(function (key) {
                    user[key] = parseInt(user[key]);
                });
                $scope.setupData.activeUsers[user.userId] = {
                    id: parseInt(user.userId),
                    loadIndicator: user.loadIndicator,
                    performanceIndicator: user.performanceIndicator,
                    performanceIndex: user.performanceIndex
                };
            });

            $scope.setupData.emptyUsersArray = $scope.setupData.activeUsers.every(isEmpty);
            $scope.data.loaded = true;
        };

        $scope.onUserSelect = function (user) {
            if ($scope.setupData.activeUsers[user.id] == undefined) {
                $scope.setupData.activeUsers[user.id] = {
                    id: parseInt(user.id),
                    loadIndicator: 100,
                    performanceIndicator: 0
                };
                $scope.setupData.emptyUsersArray = false;

                if (!$scope.setupData.existingPlan) {
                    $scope.onUserTCPDChange(user.id, user.performanceIndex);
                }
            } else {
                delete $scope.setupData.activeUsers[user.id];
                $scope.setupData.emptyUsersArray = $scope.setupData.activeUsers.every(isEmpty);
            }

            if (!$scope.setupData.existingPlan) {
                $scope.onDurationChange();
            }
        };
        $scope.onUserTCPDChange = function (userId, index) {
            var loadIndicator = $scope.setupData.activeUsers[userId].loadIndicator;
            $scope.setupData.activeUsers[userId].performanceIndicator = Math.round((index / 100) * loadIndicator);

            $scope.calculateExpectedTCPD();
            $scope.calculateActualTCPD();

            if (!$scope.setupData.emptyUsersArray) {
                $scope.durationMismatchCalculations();
                $scope.overUnderPerformCalculations();
            } else {
                kpiReporting.noty.closeAll();
                $scope.warnings = [];
            }
        };
        $scope.onDurationChange = function () {
            $scope.calculateActualTCPD();
            $scope.calculateExpectedTCPD();

            if (!$scope.setupData.emptyUsersArray) {
                $scope.durationMismatchCalculations();
                $scope.overUnderPerformCalculations();
            } else {
                kpiReporting.noty.closeAll();
                $scope.warnings = [];
            }
        };

        $scope.calculateExpectedTCPD = function () {
            $scope.setupData.expectedTCPD = Math.round($scope.setupData.activeUsers.reduce(function (a, b) {
                return a + b.performanceIndicator;
            }, 0));
        };
        $scope.calculateActualTCPD = function () {
            $scope.setupData.actualTCPD = Math.round(
                ($scope.setupData.unAllocatedTestCasesCount +
                $scope.setupData.expiredNonFinalTestCasesCount) /
                $scope.setupData.duration);

            $scope.setupData.acceptableTCPDDelta = Math.round($scope.setupData.expectedTCPD *
                (TCPDTolerance / 100));
        };

        $scope.durationMismatchCalculations = function () {
            if ($scope.setupData.unAllocatedTestCasesCount + $scope.setupData.expiredNonFinalTestCasesCount > 0 &&
                $scope.setupData.expectedTCPD > 0) {
                var suggestedDuration =
                    ($scope.setupData.unAllocatedTestCasesCount +
                    $scope.setupData.expiredNonFinalTestCasesCount) /
                    $scope.setupData.expectedTCPD;
                var suggestedDurationRounded = Math.round(suggestedDuration * 10) / 10;
                var acceptableSuggestedDurationDelta = suggestedDurationRounded * (durationTolerance / 100);

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

            if (absTCPDDelta > $scope.setupData.acceptableTCPDDelta && $scope.setupData.expectedTCPD > 0 && TCPDDelta > 0) {
                if (!$scope.warnings.underperform) {
                    $scope.warnings.underperform = kpiReporting.noty.getPermanentWarning(underPerformMsg);
                }
                if ($scope.warnings.overperform) {
                    $scope.warnings.overperform.close();
                    $scope.warnings.overperform = false;
                }
            }
            if (absTCPDDelta > $scope.setupData.acceptableTCPDDelta && $scope.setupData.expectedTCPD && TCPDDelta < 0) {
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
            var data = {
                activeUsers: $scope.setupData.activeUsers,
                duration: $scope.setupData.duration,
                algorithm: parseInt($scope.setupData.algorithm),
                expectedTCPD: $scope.setupData.expectedTCPD,
                actualTCPD: $scope.setupData.actualTCPD
            };

            setupData.saveSetup($scope.data.project.id, data).then(
                function success() {
                    kpiReporting.noty.success('Successfully saved configuration!');
                    $location.path('projects/' + $routeParams['id'] + '/daysAllocation');
                }, $scope.functions.onError
            );
        };
        $scope.resetSetup = function () {
            $scope.data.loaded = false;

            var data = {
                reason: $scope.setupData.selectedReason
            };

            setupData.resetSetup($scope.data.project.id, data).then(
                function success(result) {
                    kpiReporting.noty.closeAll();
                    kpiReporting.noty.success(result.data.msg);

                    $scope.setupData.activeUsers = [];
                    $scope.setupData.existingPlan = false;

                    $scope.getProjectDetails();

                    $scope.data.loaded = true;
                }, $scope.functions.onError
            )
        };
        $scope.clearResetReasonChoice = function () {
            $scope.setupData.selectedReason = {};
            $scope.setupData.planResetPrompt = false;
        };

        function onGetProjectDetailsSuccess(result) {
            $scope.data.project = result.data;
            $scope.data.config = result.data.config;

            $scope.setupData.existingPlan = $scope.data.config != false;

            $scope.setupData.expiredNonFinalTestCasesCount = parseInt(result.data.expiredNonFinalTestCasesCount);
            $scope.setupData.unAllocatedTestCasesCount = parseInt(result.data.unAllocatedTestCasesCount);

            $scope.setupData.currentDuration = parseFloat(result.data.currentDuration);
            $scope.setupData.taskDuration = parseFloat(result.data.taskDuration);
            $scope.setupData.initialCommitment = parseFloat(result.data.initialCommitment) || 0;

            if ($scope.setupData.initialCommitment == 0) {
                $scope.setupData.duration = $scope.setupData.taskDuration
            } else if ($scope.setupData.initialCommitment != 0 && $scope.setupData.currentDuration != 0) {
                $scope.setupData.duration = $scope.setupData.currentDuration;
            } else if ($scope.setupData.initialCommitment != 0 && $scope.setupData.currentDuration == 0) {
                $scope.setupData.duration = $scope.setupData.initialCommitment;
            }

            $scope.setupData.previousDuration = $scope.setupData.duration;

            $scope.getAllUsers();
            $scope.getResetReasons();
            $scope.suggestUsers(result.data.activeUsers);
            $scope.calculateExpectedTCPD();

            if (!$scope.setupData.existingPlan) {
                $scope.onDurationChange();
            }

            $scope.data.loaded = true;
        }

        function isEmpty(element) {
            return element == undefined;
        }

        $scope.getProjectDetails();
    });