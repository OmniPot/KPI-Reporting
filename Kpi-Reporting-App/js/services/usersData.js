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
        })
            .success(function success() {
                authentication.clearUserData();
            });
    }

    function getAllUsers() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'users/all'
        });
    }

    return {
        login: login,
        logout: logout,
        getAllUsers: getAllUsers
    }
});