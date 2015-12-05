kpiReporting.controller('LoginController', function ($scope, $location, usersData) {

    $scope.checkForRedirectDestination = function () {
        if ($scope.data.redirectToProjectStatistics) {
            $location.path('/projects/' + $scope.data.redirectToProjectStatistics + '/statistics');
        } else if ($scope.data.redirectToProjectAllocationMap) {
            $location.path('/projects/' + $scope.data.redirectToProjectAllocationMap + '/allocationMap');
        } else if ($scope.data.redirectToProjectDaysAllocation) {
            $location.path('/projects/' + $scope.data.redirectToProjectDaysAllocation + '/daysAllocation');
        }
    };

    $scope.login = function (user) {
        usersData.login(user).then(
            function success() {
                kpiReporting.noty.success('Successfully logged in.');
                $scope.checkForRedirectDestination();
                $scope.data.clearRedirectParams();
            },
            function error() {
                kpiReporting.noty.error('Login failed. Please try again.');
            });
    };
});