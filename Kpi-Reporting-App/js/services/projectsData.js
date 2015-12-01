kpiReporting.factory('projectsData', function ($http, baseServiceUrl) {

    function getAllProjects() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/all'
        });
    }

    function getProjectById(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId
        });
    }

    function getProjectSetupDetailsById(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/setupDetails'
        });
    }

    return {
        getAllProjects: getAllProjects,
        getProjectById: getProjectById,
        getProjectSetupDetailsById: getProjectSetupDetailsById
    }
});