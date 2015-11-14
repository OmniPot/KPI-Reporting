kpiReporting.factory('projectsData', function ($http, baseServiceUrl) {
    function getAllProjects() {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/all',
            headers: {'Content-Type': 'application/json'}
        });
    }

    function getById(projectId) {
        return $http({
            method: 'GET',
            url: baseServiceUrl + 'projects/' + projectId,
            headers: {'Content-Type': 'application/json'}
        });
    }

    //function addProject(projectData) {
    //    return $http({
    //        method: 'POST',
    //        url: baseServiceUrl + 'projects/create',
    //        headers: {'Content-Type': 'application/json'},
    //        data: projectData
    //    });
    //}

    return {
        getAllProjects: getAllProjects,
        getById: getById
    }
});