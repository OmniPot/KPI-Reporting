kpiReporting.controller('UsersLoadController', function ($scope, $location, $http, usersData) {

    $scope.data.loaded = false;
    $scope.usersLoadData = {
        users: [],
        alerts: []
    };

    $scope.getUsersLoad = function () {
        usersData.getUsersLoad().then(
            function success(result) {
                $scope.data.loaded = true;
                $scope.usersLoadData.users = result.data;

                $scope.calculateLoadDeltas();
            }, $scope.functions.onError
        );
    };

    $scope.calculateLoadDeltas = function () {
        $scope.usersLoadData.users.forEach(function (user) {
            if (user.userLoadPercentage > 110 || user.userLoadPercentage < 100) {
                $scope.usersLoadData.alerts[user.userId] = 'color:#FF6600;font-size:1.25em;';
            } else {
                $scope.usersLoadData.alerts[user.userId] = false;
            }
        });
    };

    $scope.getUsersLoad();
});