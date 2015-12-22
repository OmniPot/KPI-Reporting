kpiReporting.directive('statusChange', function () {
    return {
        restrict: 'A',
        controller: 'StatusesChangeController',
        templateUrl: 'templates/directives/statusChange.html'
    }
});