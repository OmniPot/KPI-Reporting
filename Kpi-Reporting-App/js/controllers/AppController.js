kpiReporting.controller('AppController', function ($rootScope, $scope, authentication, projectsData) {

    $scope.authentication = authentication;
    $scope.data = {};

    $scope.data.checkIfAllocated = function (projectId) {
        return projectsData.checkIfProjectIsAllocated(projectId);
    };

    $scope.data.clearRedirectParams = function () {
        $scope.data.redirectToProjectStatistics = undefined;
        $scope.data.redirectToProjectAllocationMap = undefined;
    };

    $scope.data.getCurrentDate = function () {
        var dateObject = new Date();
        var dd = dateObject.getDate();
        var mm = dateObject.getMonth() + 1;
        var yyyy = dateObject.getFullYear();

        return new Date(yyyy + '-' + mm + '-' + dd);
    };

    $scope.data.getCurrentDateString = function () {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1;
        var yyyy = today.getFullYear();

        return yyyy + '-' + mm + '-' + dd;
    };
});