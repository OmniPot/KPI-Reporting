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
        return $sessionStorage.user ? true : false;
    }

    function isAdmin() {
        if (!$sessionStorage.user) {
            return false;
        } else {
            if($sessionStorage.user.role == 'admin') {
                return true;
            }
        }
    }

    return {
        getUserData: getUserData,
        setUserData: setUserData,
        clearUserData: clearUserData,
        isLoggedIn: isLoggedIn,
        isAdmin: isAdmin
    };
});