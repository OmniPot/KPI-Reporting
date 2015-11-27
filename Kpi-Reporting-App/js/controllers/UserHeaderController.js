kpiReporting.controller('UserHeaderController', function ($scope, $location, authentication, usersData) {
    $scope.user = authentication.getUserData();

    $scope.logout = function () {
        usersData.logout().then(
            function success() {
                kpiReporting.noty.success('Successfully logged out.');
                $location.path('/user/login');
            },
            function error() {
                $location.path('/user/login');
            });

        $scope.authentication.clearUserData();
    };

});