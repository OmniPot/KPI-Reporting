kpiReporting.controller('ProjectStatisticsController',
    function ($scope, $location, $routeParams, projectsData) {

        // Authenticate
        if (!$scope.authentication.isLoggedIn()) {
            $scope.functions.clearRedirectParams();
            $scope.data.redirectToProjectStatistics = $routeParams['id'];
            $location.path('/login');
            return;
        }

        $scope.statsData = {};
        $scope.data.project = {};
        $scope.data.loaded = false;

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
                $scope.data.loaded = true;
            } else {
                $location.path('projects/' + $routeParams['id'] + '/setup');
            }
        }

        $scope.getProjectConfig();
    });