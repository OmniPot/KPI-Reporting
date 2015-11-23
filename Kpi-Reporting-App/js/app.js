var kpiReporting = angular.module('kpiReporting', ['ngRoute']);

kpiReporting.constant('baseServiceUrl', 'http://localhost/backend/public/');

kpiReporting.config(function ($routeProvider) {

    $routeProvider.when('/projects', {
        controller: 'ProjectsController',
        templateUrl: 'templates/projects.html'
    });

    $routeProvider.when('/projects/:id/info', {
        controller: 'ProjectInfoController',
        templateUrl: 'templates/projectInfo.html'
    });

    $routeProvider.when('/projects/:id/allocationMap', {
        controller: 'ProjectMapController',
        templateUrl: 'templates/projectMap.html'
    });

    $routeProvider.otherwise({
        redirectTo: '/projects'
    });
});