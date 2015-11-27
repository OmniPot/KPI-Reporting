<?php

namespace KPIReporting\Controllers;

use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ProjectsRepository;

class ProjectsController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/all')
     * @return string
     */
    public function getAll() {
        $projects = ProjectsRepository::getInstance()->getAllProjects();

        return $projects;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int')
     * @param int $projectId
     * @return string
     * @throws ApplicationException
     */
    public function getById( $projectId ) {
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId, $this->getCurrentDate() );

        if ( !$project ) {
            throw new ApplicationException( "No project with id {$projectId} found", 404 );
        }

        return $project;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/check')
     * @param int $projectId
     * @return string
     * @throws ApplicationException
     */
    public function checkIfProjectIsAllocated( $projectId ) {
        $project = ProjectsRepository::getInstance()->checkIfProjectIsAllocated( $projectId );

        if ( !$project ) {
            throw new ApplicationException( "No project with id {$projectId} found", 404 );
        }

        return $project;
    }

}