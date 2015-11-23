kpiReporting.controller('ProjectMapController',
    function ($scope, $location, $routeParams, projectsData, testCasesData, statusesData) {

        $scope.data.project = {};
        $scope.data.statuses = [];
        $scope.data.executable = [];

        statusesData.getAllStatuses().then(
            function onGetStatusesSuccess(result) {
                $scope.data.statuses = result.data;
            }, onError
        );

        projectsData.getProjectById($routeParams['id']).then(
            function onGetProjectSuccess(result) {
                $scope.data.project = result.data;
            }, onError
        );

        function onError(error) {
            $location.path('/projects');
            kpiReporting.noty.error(error.status + ': ' + error.data);
        }
    });