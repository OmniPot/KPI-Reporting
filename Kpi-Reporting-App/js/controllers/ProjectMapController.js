kpiReporting.controller('ProjectMapController',
    function ($scope, $location, $routeParams, projectsData, usersData, testCasesData, statusesData, daysData) {

        if (!$scope.authentication.isLoggedIn()) {
            $scope.data.clearRedirectParams();
            $scope.data.redirectToProjectAllocationMap = $routeParams['id'];
            $location.path('/login');
            return;
        }

        $scope.data.checkIfAllocated($routeParams['id']).then(
            function (result) {
                if (result.data.isAllocated == 0) {
                    $location.path('/projects/' + $routeParams['id'] + '/setup');
                }
            }
        );

        $scope.data.project = {};
        $scope.data.remainingDays = [];
        $scope.data.users = [];
        $scope.data.statuses = [];
        $scope.data.daysChanges = [];
        $scope.data.userChanges = [];
        $scope.data.statusChanges = [];
        $scope.data.expanded = [];

        $scope.getProjectById = function (projectId) {
            projectsData.getProjectById(projectId).then(
                function (result) {
                    $scope.data.project = result.data;
                }, onError
            );
        };

        $scope.getAllUsers = function () {
            usersData.getAllUsers().then(
                function (result) {
                    $scope.data.users = result.data;
                }, onError
            );
        };

        $scope.getAllStatuses = function () {
            statusesData.getAllStatuses().then(
                function (result) {
                    $scope.data.statuses = result.data;
                }, onError
            );
        };

        $scope.getProjectRemainingDays = function () {
            daysData.getProjectRemainingDays($routeParams['id']).then(
                function (result) {
                    $scope.data.remainingDays = result.data;
                }, onError
            );
        };

        $scope.getProjectById($routeParams['id']);
        $scope.getAllUsers();
        $scope.getAllStatuses();
        $scope.getProjectRemainingDays();

        $scope.expandTestCase = function (testCase) {
            testCase.expanded = true;
            testCasesData.getTestCaseEvents(testCase.testCaseId).then(
                function (result) {
                    testCase.events = result.data;
                }, onError)
        };

        $scope.collapseTestCase = function (testCase) {
            testCase.expanded = false;
        };

        function onError(error) {
            $location.path('/projects');
            kpiReporting.noty.error(error.status + ': ' + error.data);
        }
    });