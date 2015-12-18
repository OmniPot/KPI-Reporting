kpiReporting.factory('setupData', function ($http, baseServiceUrl) {

    function saveSetup(projectId, data) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'projects/' + projectId + '/setup/save',
            data: data
        });
    }

    function resetSetup(projectId, data) {
        return $http({
            method: 'POST',
            url: baseServiceUrl + 'projects/' + projectId + '/setup/clear',
            data: data
        });
    }

    function getSetupDetails(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/setup'
        });
    }

    return {
        saveSetup: saveSetup,
        resetSetup: resetSetup,
        getSetupDetails: getSetupDetails
    }
});