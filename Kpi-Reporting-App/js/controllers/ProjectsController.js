kpiReporting.controller('ProjectsController', function ($scope, $location, $routeParams, projectsData) {

    $scope.data.projects = [];

    projectsData.getAllProjects().then(
        onGetAllProjectsSuccess,
        onError
    );

    function onGetAllProjectsSuccess(result) {
        $scope.data.projects = result.data;
    }

    function onError(error) {
        $location.path('/projects');
        kpiReporting.noty.error(error.status + ': ' + error.data);
    }
});