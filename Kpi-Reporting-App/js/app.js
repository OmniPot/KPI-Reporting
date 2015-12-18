var kpiReporting = angular.module('kpiReporting', ['ngRoute', 'ngStorage', 'angularSpinner']);

kpiReporting.constant('baseServiceUrl', 'http://' + getServerAddress() + '/backend/public/');

kpiReporting.constant('durationTolerance', 5);
kpiReporting.constant('TCPDTolerance', 10);
kpiReporting.constant('spinConfig', '' +
    "{lines: 20," +
    " length: 0," +
    " width: 4," +
    " radius: 10," +
    " scale: 1.25," +
    " corners: 1," +
    " opacity: 0," +
    " color: '#f00'," +
    " speed: 1," +
    " trail: 80," +
    " top: '50%'," +
    " left: '50%'}");

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
