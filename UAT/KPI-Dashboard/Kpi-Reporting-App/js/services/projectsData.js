kpiReporting.factory('projectsData', function ($http, baseServiceUrl) {

    function getProjectById(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId
        });
    }

    function getActiveConfig(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/config'
        });
    }

    return {
        getProjectById: getProjectById,
        getActiveConfig: getActiveConfig
    }
});