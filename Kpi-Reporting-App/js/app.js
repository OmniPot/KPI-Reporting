var kpiReporting = angular.module('kpiReporting', ['ngRoute', 'ngStorage', 'angularSpinner']);

kpiReporting.constant('baseServiceUrl', 'http://' + getServerAddress() + '/backend/public/');

kpiReporting.constant('durationTolerance', 5);
kpiReporting.constant('TCPDTolerance', 10);

kpiReporting.config(function ($routeProvider) {

    $routeProvider.when('/login', {
        controller: 'LoginController',
        templateUrl: 'templates/directives/loginPage.html',
        caseInsensitiveMatch: true
    });

    $routeProvider.when('/projects/:id/statistics', {
        controller: 'ProjectStatisticsController',
        templateUrl: 'templates/projectStatistics.html',
        caseInsensitiveMatch: true
    });

    $routeProvider.when('/projects/:id/allocationMap', {
        controller: 'ProjectMapController',
        templateUrl: 'templates/projectMap.html',
        caseInsensitiveMatch: true
    });

    $routeProvider.when('/projects/:id/setup', {
        controller: 'ProjectSetupController',
        templateUrl: 'templates/projectSetup.html',
        caseInsensitiveMatch: true
    });

    $routeProvider.when('/projects/:id/daysAllocation', {
        controller: 'ProjectDaysController',
        templateUrl: 'templates/projectDays.html',
        caseInsensitiveMatch: true
    });

    $routeProvider.otherwise({
        redirectTo: '/login'
    });
});

kpiReporting.run(function ($rootScope) {
    $rootScope.$on('$locationChangeStart', function (event) {
        kpiReporting.noty.closeAll();
    });
});
