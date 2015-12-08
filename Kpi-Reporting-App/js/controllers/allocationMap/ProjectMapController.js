kpiReporting.controller('ProjectMapController',
    function ($scope, $location, $routeParams, projectsData, usersData, testCasesData, statusesData, daysData) {

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

        $scope.getProjectActiveConfig = function (projectId) {
            projectsData.getProjectConfig(projectId).then(
                function success(result) {
                    if (!result.data.configId) {
                        $location.path('/projects/' + $routeParams['id'] + '/setup');
                    } else {
                        $scope.loadProjectAllocationMap(projectId);
                    }
                }, $scope.functions.onError
            );
        };

        if (!$scope.authentication.isLoggedIn()) {
            $scope.functions.clearRedirectParams();
            $scope.data.redirectToProjectAllocationMap = $routeParams['id'];
            $location.path('/login');
        } else {
            $scope.getProjectActiveConfig($routeParams['id']);
        }

        $scope.loadProjectAllocationMap = function (projectId) {
            $scope.getProjectById(projectId);
            $scope.getAllUsers();
            $scope.getAllStatuses();
            $scope.getProjectRemainingDays();
        };
        $scope.getProjectById = function (projectId) {
            projectsData.getProjectById(projectId).then(
                function onProjectFetchSuccess(result) {
                    $scope.data.project = result.data;
                    testCasesData.getProjectTestCases(projectId).then(function onTestCasesFetchSuccess(result) {
                        $scope.data.testCases = result.data;
                    }, $scope.functions.onError);
                }, $scope.functions.onError
            );
        };
        $scope.getAllUsers = function () {
            usersData.getAllUsers().then(
                function success(result) {
                    $scope.data.users = result.data;
                }, $scope.functions.onError
            );
        };
        $scope.getAllStatuses = function () {
            statusesData.getAllStatuses().then(
                function success(result) {
                    $scope.data.statuses = result.data;
                }, $scope.functions.onError
            );
        };
        $scope.getProjectRemainingDays = function () {
            daysData.getProjectRemainingDays($routeParams['id']).then(
                function success(result) {
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
    });