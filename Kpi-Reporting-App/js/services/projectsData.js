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

    function checkIfProjectIsAllocated(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId + '/check'
        });
    }

    return {
        getAllProjects: getAllProjects,
        getProjectById: getProjectById,
        checkIfProjectIsAllocated: checkIfProjectIsAllocated
    }
});