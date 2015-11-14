kpiReporting.controller('ProjectsController', function ($scope, $location, $routeParams, projectsData) {

    var projectId = $routeParams['id'];

    $scope.projects = [];

    if (projectId) {
        projectsData.getById(projectId).then(
            function (result) {
                $scope.project = result.data;
            },
            function (error) {
                $location.path('/projects');
                kpiReporting.noty.error(error.status + ': ' + error.data);
            }
        )
    } else {
        projectsData.getAllProjects().then(
            function (result) {
                $scope.projects = result.data;
            },
            function (error) {
                $location.path('/projects');
                kpiReporting.noty.error(error.status + ': ' + error.data);
            }
        );
    }
});