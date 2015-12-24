kpiReporting.controller('UserHeaderController', function ($scope, $location, $routeParams, authentication, usersData) {
    $scope.data.user = authentication.getUserData();

    $scope.logout = function () {
        usersData.logout().then(
            function success() {
                kpiReporting.noty.success('Successfully logged out.');
                $location.path('/login');
            },
            function error() {
                $location.path('/login');
            });

        $scope.authentication.clearUserData();
    };

});