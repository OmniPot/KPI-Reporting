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

    function extendProjectDuration(projectId, data) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'projects/' + projectId + '/extendDuration',
            data: data
        });
    }

    return {
        getProjectRemainingDays: getProjectRemainingDays,
        getProjectAllocatedDays: getProjectAllocatedDays,
        extendProjectDuration: extendProjectDuration
    }
});