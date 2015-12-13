kpiReporting.controller('ProjectMapController',
    function ($scope, $location, $routeParams, projectsData, usersData, testCasesData, statusesData, daysData) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.functions.clearRedirectParams();
            $scope.data.redirectToProjectAllocationMap = $routeParams['id'];
            $location.path('/login');
            return;
        }

        $scope.data = {
            project: {},
            remainingDays: [],
            users: [],
            statuses: [],
            daysChanges: [],
            userChanges: [],
            statusChanges: [],
            expanded: [],
            unallocated: 0
        };

        $scope.getProjectConfig = function () {
            projectsData.getActiveConfig($routeParams['id']).then(onGetProjectConfigSuccess, $scope.functions.onError);
        };
        $scope.getProjectById = function () {
            projectsData.getProjectById($routeParams['id']).then(onGetProjectSuccess, $scope.functions.onError);
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
                $scope.data.unallocated = parseInt(result.data.unAllocatedTestCasesCount);

                $scope.getProjectTestCases();
                $scope.getAllUsers();
                $scope.getAllStatuses();
                $scope.getProjectRemainingDays();
            } else {
                $location.path('projects/' + $routeParams['id'] + '/setup');
            }
        }

        $scope.getProjectTestCases = function () {
            testCasesData.getProjectTestCases($routeParams['id']).then(
                function success(result) {
                    $scope.data.testCases = result.data;
                }, $scope.functions.onError
            )
        };
        $scope.getAllUsers = function () {
            usersData.getAllUsers().then(
                function (result) {
                    $scope.data.users = result.data;
                }, $scope.functions.onError
            );
        };
        $scope.getAllStatuses = function () {
            statusesData.getAllStatuses().then(
                function (result) {
                    $scope.data.statuses = result.data;
                }, $scope.functions.onError
            );
        };
        $scope.getProjectRemainingDays = function () {
            daysData.getProjectRemainingDays($routeParams['id']).then(
                function (result) {
                    $scope.data.remainingDays = result.data;
                }, $scope.functions.onError
            );
        };

        $scope.expandTestCase = function (testCase) {
            testCase.expanded = true;
            testCasesData.getTestCaseEvents(testCase.testCaseId).then(
                function (result) {
                    testCase.events = result.data;
                }, $scope.functions.onError
            )
        };
        $scope.collapseTestCase = function (testCase) {
            testCase.expanded = false;
        };

        $scope.getProjectConfig();
    });