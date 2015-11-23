kpiReporting.factory('testCasesData', function ($http, baseServiceUrl) {

    function getProjectAllocations($projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + $projectId + '/allocationMap',
            headers: {'Content-Type': 'application/json'}
        });
    }

    return {
        getProjectTestCases: getProjectAllocations
    }
});