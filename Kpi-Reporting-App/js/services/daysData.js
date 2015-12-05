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

    return {
        getProjectRemainingDays: getProjectRemainingDays,
        getProjectAllocatedDays: getProjectAllocatedDays
    }
});