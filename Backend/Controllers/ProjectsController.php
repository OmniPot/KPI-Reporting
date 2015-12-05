<?php

namespace KPIReporting\Controllers;

use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
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
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );
        $activeConfig = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $testCases = ProjectsRepository::getInstance()->getProjectTestCases( $projectId, $this->getCurrentDate() );

        if ( !$project ) {
            throw new ApplicationException( "No project with id {$projectId} found", 404 );
        }

        $project[ 'testCases' ] = $testCases;
        $project[ 'config' ] = $activeConfig;

        return $project;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/setupDetails')
     * @param int $projectId
     * @return string
     * @throws ApplicationException
     */
    public function getByIdSetupDetails( $projectId ) {
        $users = ProjectsRepository::getInstance()->getProjectById( $projectId );

        if ( !$users ) {
            throw new ApplicationException( "No project with id {$projectId} found", 404 );
        }

        return $users;
    }

}