kpiReporting.factory('testCasesData', function ($http, baseServiceUrl) {

    function getProjectTestCases(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/allocationMap',
        });
    }

    function getTestCaseEvents(testCaseId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'testCases/' + testCaseId + '/events',
        });
    }

    function changeTestCaseUser(userChangeData) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'testCases/changeUser',
            data: userChangeData
        });
    }

    function changeTestCaseStatus(statusChangeData) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'testCases/changeStatus',
            data: statusChangeData
        });
    }

    function changeTestCaseDate(dateChangeData) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'testCases/changeDate',
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