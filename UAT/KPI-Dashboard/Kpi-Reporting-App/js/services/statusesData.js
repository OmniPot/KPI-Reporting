kpiReporting.factory('statusesData', function ($http, baseServiceUrl) {

    function getAllStatuses() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'statuses/all',
            headers: {'Content-Type': 'application/json'}
        });
    }

    return {
        getAllStatuses: getAllStatuses
    }
});