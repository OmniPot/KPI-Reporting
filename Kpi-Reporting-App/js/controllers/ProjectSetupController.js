kpiReporting.controller('ProjectSetupController',
    function ($scope, $location, $routeParams, projectsData, usersData, setupData) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $location.path('/login');
            return;
        }

        var tcWarningMsg = 'Test cases per day change tolerance exceeded. Saving this configuration will reset your commitment plan.';
        var durationWarningMsg = 'Duration change tolerance exceeded. Saving this configuration will reset your commitment plan.';

        $scope.warnings = {
            duration: null,
            tcpd: null
        };
        $scope.setupData = {
            project: {},
            activeUsers: [],
            durationTolerance: 5,
            testCaseTolerance: 10,
            planReset: false
        };

        // On-load callbacks
        $scope.getProjectSetupDetailsById = function (projectId) {
            projectsData.getProjectSetupDetailsById(projectId).then(
                function success(result) {
                    $scope.setupData.project = result.data;
                    $scope.setupData.duration = parseInt(result.data.duration);

                    $scope.prepareDurationCalculations();
                    $scope.prepareTotalTCPDCalculations();

                    $scope.setupData.testCasesCount = result.data.testCasesCount;
                    $scope.calculateSuggestedDuration();
                }, $scope.data.onError
            );
        };
        $scope.getAllUsers = function () {
            usersData.getAllUsers().then(
                function success(result) {
                    $scope.setupData.allUsers = result.data;
                }, $scope.data.onError
            );
        };
        $scope.suggestUsers = function (users) {
            users.forEach(function (user) {
                Object.keys(user).forEach(function (key) {
                    user[key] = parseInt(user[key]);
                });
                $scope.setupData.activeUsers[user.userId] = {
                    load: user.loadIndicator,
                    performance: user.performanceIndicator
                };
            });

            $scope.calculateTotalTCPD();
        };
        $scope.prepareDurationCalculations = function () {
            $scope.setupData.previousDuration = $scope.setupData.duration;
            $scope.setupData.acceptableDurationDelta = $scope.setupData.previousDuration *
                ($scope.setupData.durationTolerance / 100);
            $scope.durationWarningCalculations();
        };
        $scope.prepareTotalTCPDCalculations = function () {
            $scope.setupData.previousTotalTCPD = $scope.setupData.totalTCPD;
            $scope.setupData.acceptableTCPDDelta = $scope.setupData.previousTotalTCPD *
                ($scope.setupData.testCaseTolerance / 100);
            $scope.TCPDWarningCalculations();
        };

        // On-change callbacks
        $scope.checkUncheck = function (user) {
            if ($scope.setupData.activeUsers[user.id] == undefined) {
                $scope.setupData.activeUsers[user.id] = {load: 0, performance: 0};
            } else {
                delete $scope.setupData.activeUsers[user.id];
                $scope.calculateTotalTCPD();
                $scope.calculateSuggestedDuration();
                $scope.TCPDWarningCalculations();
                $scope.durationWarningCalculations();
            }
        };
        $scope.calculateUserTCPD = function (userId, index) {
            var load = $scope.setupData.activeUsers[userId].load;
            $scope.setupData.activeUsers[userId].performance = Math.round((index / 100) * load);
            $scope.calculateTotalTCPD();
            $scope.calculateSuggestedDuration();
            $scope.TCPDWarningCalculations();
        };
        $scope.calculateTotalTCPD = function () {
            $scope.setupData.totalTCPD = Math.round($scope.setupData.activeUsers.reduce(function (a, b) {
                return a + b.performance;
            }, 0));
        };
        $scope.calculateSuggestedDuration = function () {
            $scope.setupData.suggestedDuration =
                Math.ceil($scope.setupData.testCasesCount / $scope.setupData.totalTCPD);
        };

        // Warning calculation callbacks
        $scope.TCPDWarningCalculations = function () {
            $scope.setupData.TCPDDelta = $scope.setupData.totalTCPD - $scope.setupData.previousTotalTCPD;
            if (Math.abs($scope.setupData.TCPDDelta) > $scope.setupData.acceptableTCPDDelta && !$scope.warnings.tcpd) {
                $scope.warnings.tcpd = kpiReporting.noty.getWarning(tcWarningMsg);
            } else if (Math.abs($scope.setupData.TCPDDelta) <= $scope.setupData.acceptableTCPDDelta && $scope.warnings.tcpd) {
                $scope.warnings.tcpd.close();
                $scope.warnings.tcpd = false;
            }
        };
        $scope.durationWarningCalculations = function () {
            $scope.setupData.durationDelta = $scope.setupData.duration - $scope.setupData.previousDuration;
            if (Math.abs($scope.setupData.durationDelta) > $scope.setupData.acceptableDurationDelta && !$scope.warnings.duration) {
                $scope.warnings.duration = kpiReporting.noty.getWarning(durationWarningMsg);
            } else if (Math.abs($scope.setupData.durationDelta) <= $scope.setupData.acceptableDurationDelta && $scope.warnings.duration) {
                $scope.warnings.duration.close();
                $scope.warnings.duration = false;
            }
        };
        $scope.save = function () {
            var data = {
                activeUsers: $scope.setupData.activeUsers,
                duration: $scope.setupData.project.duration
            };

            console.log($scope.setupData);

            //setupData.save($scope.setupData.project.id, data).then(
            //    function success(result) {
            //        kpiReporting.noty.success(result.data);
            //    }, $scope.data.onError
            //);
        };

        setupData.getSetupDetails($routeParams['id']).then(
            function success(result) {
                $scope.getProjectSetupDetailsById($routeParams['id']);
                $scope.getAllUsers();
                $scope.suggestUsers(result.data);
                $scope.calculateTotalTCPD();
            }, $scope.data.onError
        );
    });