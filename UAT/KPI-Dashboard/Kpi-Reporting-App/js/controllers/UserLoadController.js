kpiReporting.controller('UserLoadController', function ($scope, $location, $routeParams, $http, usersData) {

    $scope.data.loaded = false;
    $scope.userData = {
        alerts: []
    };


    $scope.getUserById = function (id) {
        usersData.getUserById(id).then(
            function success(result) {
                $scope.userData.info = result.data;

                $scope.getUserLoad(result.data.userId);
            }, $scope.functions.onError
        )
    };

    $scope.getUserLoad = function (id) {
        usersData.getUserLoad(id).then(
            function success(result) {
                $scope.data.loaded = true;
                $scope.userData.days = result.data;

                $scope.calculateLoadDeltas(result.data);
            }, $scope.functions.onError
        );
    };

    $scope.calculateLoadDeltas = function (days) {
        $scope.userData.alerts = [];
        days.forEach(function (day) {
            var tolerance = Math.round(day.expected * (10 / 100));
            var delta = day.expected - day.allocated;

            if (day.period == 1 || day.period == 2) {
                if (delta > tolerance) {
                    $scope.userData.alerts[day.dayId] = 'color:#FF6600;font-size:1.25em;';
                } else if (delta >= -tolerance && delta <= tolerance) {
                    $scope.userData.alerts[day.dayId] = false;
                } else if (delta < -tolerance) {
                    $scope.userData.alerts[day.dayId] = 'color:#66CD00;font-size:1.25em;';
                }
            } else {
                delta = Math.abs(day.expected - day.allocated);
                if (delta > tolerance) {
                    $scope.userData.alerts[day.dayId] = 'color:#FF6600;font-size:1.25em;';
                } else {
                    $scope.userData.alerts[day.dayId] = false;
                }
            }
        });

        console.log($scope.userData);
    };
    $scope.expandUserDay = function (dayDate) {
        usersData.expandUserDay($routeParams['id'], dayDate).then(
            function success(result) {
                console.log(result);
            }, $scope.functions.onError
        )
    };

    $scope.getUserById($routeParams['id']);
});