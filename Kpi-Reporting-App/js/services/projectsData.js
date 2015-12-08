kpiReporting.factory('projectsData', function ($http, baseServiceUrl) {

    function getProjectById(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId
        });
    }

    function getProjectConfig(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/config'
        });
    }

    function getProjectDetails(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/setupDetails'
        });
    }

    return {
        getProjectById: getProjectById,
        getProjectConfig: getProjectConfig,
        getProjectDetails: getProjectDetails
    }
});