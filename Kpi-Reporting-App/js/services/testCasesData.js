kpiReporting.factory('testCasesData', function ($http, baseServiceUrl) {

    function getProjectTestCases(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/allocationMap'
        });
    }

    function getTestCaseEvents(testCaseId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'testCases/' + testCaseId + '/events'
        });
    }

    function changeTestCaseUser(projectId, userChangeData) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'projects/' + projectId + '/testCases/changeUser',
            data: userChangeData
        });
    }

    function changeTestCaseStatus(projectId, statusChangeData) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'projects/' + projectId + '/testCases/changeStatus',
            data: statusChangeData
        });
    }

    function changeTestCaseDate(projectId, dateChangeData) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'projects/' + projectId + '/testCases/changeDate',
            data: dateChangeData
        });
    }

    return {
        getProjectTestCases: getProjectTestCases,
        getTestCaseEvents: getTestCaseEvents,
        changeTestCaseStatus: changeTestCaseStatus,
        changeTestCaseUser: changeTestCaseUser,
        changeTestCaseDate: changeTestCaseDate
    }
});