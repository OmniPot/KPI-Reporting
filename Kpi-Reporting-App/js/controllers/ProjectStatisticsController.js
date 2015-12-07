kpiReporting.controller('ProjectStatisticsController',
    function ($scope, $location, $routeParams, projectsData) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.data.clearRedirectParams();
            $scope.data.redirectToProjectStatistics = $routeParams['id'];
            $location.path('/login');
            return;
        }

        $scope.data = {};
        $scope.data.project = {};

        $scope.getProjectById = function (projectId) {
            projectsData.getProjectDetails(projectId).then(
                function success(result) {
                    if (result.data.config == false) {
                        $location.path('/projects/' + $routeParams['id'] + '/setup');
                        return;
                    }
                    $scope.data.project = result.data;

                },$scope.functions.onError);
        };

        $scope.getProjectById($routeParams['id']);
    });