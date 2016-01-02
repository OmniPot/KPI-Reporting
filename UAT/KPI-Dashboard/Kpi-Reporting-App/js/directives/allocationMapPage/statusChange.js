kpiReporting.directive('statusChange', function () {
    return {
        restrict: 'A',
        controller: 'StatusesChangeController',
        templateUrl: 'templates/allocationMapPage/statusChange.html'
    }
});