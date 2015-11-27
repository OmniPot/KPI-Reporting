var kpiReporting = angular.module('kpiReporting', ['ngRoute', 'ngStorage']);

kpiReporting.constant('baseServiceUrl', 'http://' + getServerAddress() + '/backend/public/');

kpiReporting.config(function ($routeProvider) {

    $routeProvider.when('/login', {
        controller: 'LoginController',
        templateUrl: 'templates/directives/loginPage.html'
    });

    $routeProvider.when('/projects/:id/statistics', {
        controller: 'ProjectStatisticsController',
        templateUrl: 'templates/projectStatistics.html'
    });

    $routeProvider.when('/projects/:id/setup', {
        controller: 'ProjectSetupController',
        templateUrl: 'templates/projectSetup.html'
    });

    $routeProvider.when('/projects/:id/allocationMap', {
        controller: 'ProjectMapController',
        templateUrl: 'templates/projectMap.html'
    });

    $routeProvider.otherwise({
        redirectTo: '/login'
    });
});
