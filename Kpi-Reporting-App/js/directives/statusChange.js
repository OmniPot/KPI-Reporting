kpiReporting.directive('statusChange', function () {
    return {
        restrict: 'A',
        controller: 'StatusesController',
        templateUrl: 'templates/directives/statusChange.html'
    }
});