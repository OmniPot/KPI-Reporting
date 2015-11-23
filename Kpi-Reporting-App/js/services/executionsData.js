kpiReporting.factory('executionsData', function ($http, baseServiceUrl) {

    function getProjectExecutions(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/executions'
        });
    }

    function executeTestCase(executionData) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'testCases/execute',
            data: executionData
        });
    }

    return {
        getProjectExecutions: getProjectExecutions,
        executeTestCase: executeTestCase
    }
});