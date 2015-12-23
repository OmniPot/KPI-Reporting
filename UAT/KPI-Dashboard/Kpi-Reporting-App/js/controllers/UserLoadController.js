kpiReporting.controller('UserLoadController', function ($scope, $location, $routeParams, $http, usersData) {

    $scope.data.loaded = false;
    $scope.userData = {
        alerts: [],
        expanded: []
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
            if (day.period == 1) {
                if ((day.expected * 0.9) - day.executed > 0) {
                    day.class = 'alert';
                } else {
                    day.class = false;
                }
            } else {
                if ((day.expected * 0.9) - day.allocated > 0) {
                    day.class = 'alert';
                } else if (day.expected * 1.5 - day.allocated < 0 && day.expected * 2 - day.allocated >= 0) {
                    day.class = 'fontRed'
                } else if ((day.expected * 2) - day.allocated < 0 && day.expected * 2.5 - day.allocated >= 0) {
                    day.class = 'fontBold'
                } else if ((day.expected * 2.5) - day.allocated < 0 && day.expected * 3 - day.allocated >= 0) {
                    day.class = 'backgroundYellow'
                } else {
                    day.class = false;
                }
            }
        });
    };
    $scope.expandUserDay = function (userId, day) {
        usersData.expandUserDay(userId, day.dayDate).then(
            function success(result) {
                $scope.userData.expanded[day.dayId] = result.data;
            }, $scope.functions.onError
        );
    };
    $scope.hideExpanded = function (dayId) {
        delete $scope.userData.expanded[dayId];
    };

    $scope.getUserById($routeParams['id']);
});