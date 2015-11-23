kpiReporting.controller('ProjectInfoController', function ($scope, $location, $routeParams, projectsData) {

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