kpiReporting.factory('authentication', function ($localStorage) {
    function setUserData(data) {
        $localStorage.user = data;
    }

    function getUserData() {
        return $localStorage.user;
    }

    function clearUserData() {
        $localStorage.$reset();
    }

    function isLoggedIn() {
        return this.getUserData() ? true : false;
    }

    return {
        getUserData: getUserData,
        setUserData: setUserData,
        clearUserData: clearUserData,
        isLoggedIn: isLoggedIn
    };
});