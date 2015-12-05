kpiReporting.controller('ProjectMapController',
    function ($scope, $location, $routeParams, projectsData, usersData, testCasesData, statusesData, daysData) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.data.clearRedirectParams();
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
            expanded: []
        };

        $scope.getProjectById = function (projectId) {
            projectsData.getProjectById(projectId).then(
                function (result) {
                    $scope.data.project = result.data;

                    if (result.data.config == false) {
                        $location.path('/projects/' + $routeParams['id'] + '/setup');
                        return;
                    }

                }, $scope.data.onError
            );
        };
        $scope.getAllUsers = function () {
            usersData.getAllUsers().then(
                function (result) {
                    $scope.data.users = result.data;
                }, $scope.data.onError
            );
        };
        $scope.getAllStatuses = function () {
            statusesData.getAllStatuses().then(
                function (result) {
                    $scope.data.statuses = result.data;
                }, $scope.data.onError
            );
        };
        $scope.getProjectRemainingDays = function () {
            daysData.getProjectRemainingDays($routeParams['id']).then(
                function (result) {
                    $scope.data.remainingDays = result.data;
                }, $scope.data.onError
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
                }, $scope.data.onError
            )
        };
        $scope.collapseTestCase = function (testCase) {
            testCase.expanded = false;
        };
    });