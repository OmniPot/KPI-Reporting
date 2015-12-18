kpiReporting.factory('daysData', function ($http, baseServiceUrl) {

    function getProjectRemainingDays(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/remainingDays'
        });
    }

    function getProjectAllocatedDays(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/allocatedDays'
        });
    }

    function getExtensionReasons() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'extensionReasons'
        });
    }

    function getResetReasons() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'resetReasons'
        });
    }

    function getParkReasons() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'parkReasons'
        });
    }

    function extendProjectDuration(projectId, data) {
        return $http({
            method: 'PUT',
            url: baseServiceUrl + 'projects/' + projectId + '/extendDuration',
            data: data
        });
    }

    function overrideConfiguration(projectId) {
        return $http({
            method: 'PUT',
            url: baseServiceUrl + 'projects/' + projectId + '/overrideConfiguration'
        });
    }

    return {
        getProjectRemainingDays: getProjectRemainingDays,
        getProjectAllocatedDays: getProjectAllocatedDays,
        getExtensionReasons: getExtensionReasons,
        getResetReasons: getResetReasons,
        getParkReasons: getParkReasons,
        extendProjectDuration: extendProjectDuration,
        overrideConfiguration: overrideConfiguration
    }
});