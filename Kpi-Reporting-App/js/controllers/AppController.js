kpiReporting.controller('AppController', function ($rootScope, $scope, $location, $sessionStorage, authentication) {

    $scope.authentication = authentication;
    $scope.data = {};
    $scope.functions = {};

    $scope.functions.clearRedirectParams = function () {
        $scope.data.redirectToProjectStatistics = undefined;
        $scope.data.redirectToProjectAllocationMap = undefined;
    };

    $scope.functions.addDays = function (date, days) {
        return new Date(date.getTime() + days * 24 * 60 * 60 * 1000);
    };

    $scope.functions.getDateFromDatetime = function (dateObject) {
        var yyyy = dateObject.getFullYear();
        var mm = dateObject.getMonth();
        var dd = dateObject.getDate();

        return new Date(Date.UTC(yyyy, mm, dd, 0, 0, 0));
    };

    $scope.functions.formatDate = function (dateObject) {
        var year = dateObject.getFullYear();
        var month = dateObject.getMonth() + 1;
        var date = dateObject.getDate();

        month = month < 10 ? '0' + month : month;
        date = date < 10 ? '0' + date : date;

        return year + '-' + month + '-' + date;
    };

    $scope.functions.resolveCanEdit = function (testCase) {
        var currentDate = $scope.functions.getDateFromDatetime(new Date());
        var testCaseDate = new Date(testCase.dayDate);
        return currentDate <= testCaseDate ? 1 : 0;
    };

    $scope.functions.onError = function (error) {
        if (error.status == 403) {
            $location.path('/login');
            $sessionStorage.$reset();
        } else {
            kpiReporting.noty.error(error.status + ': ' + error.data);
        }
    };
});