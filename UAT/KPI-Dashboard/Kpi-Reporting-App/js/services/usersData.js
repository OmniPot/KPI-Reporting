kpiReporting.factory('usersData', function ($http, baseServiceUrl, authentication) {

    function login(loginData) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'user/login',
            data: loginData
        })
            .success(function success(data) {
                authentication.setUserData(data);
            });
    }

    function logout() {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'user/logout'
        }).success(function success() {
            authentication.clearUserData();
        });

    }

    function getAllUsers() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'users/all'
        });
    }

    function getUsersLoad() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'users/load'
        });
    }

    function getUserById(userId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'user/' + userId
        });
    }

    function getUserLoad(userId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'users/' + userId + '/load'
        });
    }

    function expandUserDay(userId, dayDate) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'user/' + userId + '/date/' + dayDate
        });
    }

    return {
        login: login,
        logout: logout,
        getAllUsers: getAllUsers,
        getUserById: getUserById,
        getUsersLoad: getUsersLoad,
        getUserLoad: getUserLoad,
        expandUserDay: expandUserDay
    }
});