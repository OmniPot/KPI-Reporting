kpiReporting.controller('ProjectStatisticsController',
    function ($scope, $location, $routeParams, projectsData) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.data.clearRedirectParams();
            $scope.data.redirectToProjectStatistics = $routeParams['id'];
            $location.path('/login');
            return;
        }

        // Check if project setup is finalized and if not redirect to setup

        $scope.data = {};
        $scope.data.project = {};

        $scope.getProjectById = function (projectId) {
            projectsData.getProjectById(projectId).then(
                function success(result) {
                    $scope.data.project = result.data;
                }, $scope.data.onError);
        };

        $scope.getProjectById($routeParams['id']);
    });