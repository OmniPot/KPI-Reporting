kpiReporting.factory('daysData', function ($http, baseServiceUrl) {

    function getProjectRemainingDays($projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + $projectId + '/remainingDays'
        });
    }

    return {
        getProjectRemainingDays: getProjectRemainingDays
    }
});