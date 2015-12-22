kpiReporting.factory('authentication', function ($sessionStorage) {
    function setUserData(data) {
        $sessionStorage.user = data;
    }

    function getUserData() {
        return $sessionStorage.user;
    }

    function clearUserData() {
        $sessionStorage.$reset();
    }

    function isLoggedIn() {
        return this.getUserData() ? true : false;
    }

    function isAdmin() {
        return this.getUserData().role == 'admin';
    }

    return {
        getUserData: getUserData,
        setUserData: setUserData,
        clearUserData: clearUserData,
        isLoggedIn: isLoggedIn,
        isAdmin: isAdmin
    };
});