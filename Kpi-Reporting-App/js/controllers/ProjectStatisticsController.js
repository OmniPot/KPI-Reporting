kpiReporting.controller('ProjectStatisticsController', function ($scope, $location, $routeParams, projectsData) {

    if (!$scope.authentication.isLoggedIn()) {
        $scope.data.clearRedirectParams();
        $scope.data.redirectToProjectStatistics = $routeParams['id'];
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

    $scope.data = {};
    $scope.data.project = {};

    projectsData.getProjectById($routeParams['id']).then(onSuccess, onError);

    function onSuccess(result) {
        $scope.data.project = result.data;
    }

    function onError(error) {
        $location.path('/projects');
        kpiReporting.noty.error(error.status + ': ' + error.data);
    }
});