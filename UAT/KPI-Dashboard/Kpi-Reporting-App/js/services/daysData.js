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

    function getAvailableDays(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/availableDates'
        });
    }

    function changeDayDate(dayId, data) {
        return $http({
            method: 'PUT',
            url: baseServiceUrl + 'days/' + dayId + '/changeDate',
            data: data
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

    function stopProjectExecution(projectId, data) {
        return $http({
            method: 'PUT',
            url: baseServiceUrl + 'projects/' + projectId + '/stopExecution',
            data: data
        });
    }


    function resumeProjectExecution(projectId) {
        return $http({
            method: 'PUT',
            url: baseServiceUrl + 'projects/' + projectId + '/resumeExecution'
        });
    }

    function deleteDay(projectId, dayId) {
        return $http({
            method: 'DELETE',
            url: baseServiceUrl + 'projects/' + projectId + '/days/' + dayId + '/delete'
        });
    }

    return {
        getProjectRemainingDays: getProjectRemainingDays,
        getProjectAllocatedDays: getProjectAllocatedDays,
        getExtensionReasons: getExtensionReasons,
        getResetReasons: getResetReasons,
        getParkReasons: getParkReasons,
        getAvailableDays: getAvailableDays,
        changeDayDate: changeDayDate,
        extendProjectDuration: extendProjectDuration,
        overrideConfiguration: overrideConfiguration,
        stopProjectExecution: stopProjectExecution,
        resumeProjectExecution: resumeProjectExecution,
        deleteDay: deleteDay
    }
});