var kpiReporting = angular.module('kpiReporting', ['ngRoute']);

kpiReporting.constant('baseServiceUrl', 'http://localhost/backend/public/');

kpiReporting.config(function ($routeProvider) {

    $routeProvider.when('/projects/:id/', {
        controller: 'ProjectsController',
        templateUrl: 'templates/project.html'
    });

    $routeProvider.when('/projects', {
        controller: 'ProjectsController',
        templateUrl: 'templates/projects.html'
    });

    $routeProvider.otherwise({
        redirectTo: '/projects'
    });
});