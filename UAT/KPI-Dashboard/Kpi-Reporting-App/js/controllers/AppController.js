kpiReporting.controller('AppController',
    function ($scope, $rootScope, $location, $http, $routeParams, $sessionStorage, authentication, usSpinnerService, spinConfig) {

        $rootScope.authentication = authentication;
        $scope.authentication = authentication;
        $scope.spinConfig = spinConfig;

        $scope.data = {};
        $scope.functions = {};

        $scope.functions.clearRedirectParams = function () {
            $scope.data.redirectToProjectStatistics = undefined;
            $scope.data.redirectToProjectAllocationMap = undefined;
            $scope.data.redirectToProjectDays = undefined;
            $scope.data.redirectToProjectSetup = undefined;
        };
        $scope.functions.getDateFromDatetime = function (dateObject) {
            var yyyy = dateObject.getFullYear();
            var mm = dateObject.getMonth();
            var dd = dateObject.getDate();

            return new Date(Date.UTC(yyyy, mm, dd, 0, 0, 0));
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
                kpiReporting.noty.error(error.data);
            }
            $scope.data.loaded = true;
        };
    });