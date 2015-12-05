kpiReporting.controller('AppController', function ($rootScope, $scope, $location, $sessionStorage, authentication) {

    $scope.authentication = authentication;
    $scope.data = {};

    $scope.data.clearRedirectParams = function () {
        $scope.data.redirectToProjectStatistics = undefined;
        $scope.data.redirectToProjectAllocationMap = undefined;
    };

    $scope.data.addDays = function (date, days) {
        return new Date(date.getTime() + days * 24 * 60 * 60 * 1000);
    };

    $scope.data.onError = function (error) {
        if (error.status == 403) {
            $location.path('/login');
            $sessionStorage.$reset();
        } else {
            kpiReporting.noty.error(error.status + ': ' + error.data);
        }
    }
});