<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ProjectBindingModel;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ProjectsRepository;

class ProjectsController extends BaseController {

    /**
     * @method GET
     * @customRoute('projects/all')
     * @return string
     */
    public function getAll() {
        $projects = ProjectsRepository::getInstance()->getAllProjects();

        return $projects;
    }

    /**
     * @method GET
     * @customRoute('projects/int')
     * @param int $projectId
     * @return string
     * @throws ApplicationException
     */
    public function getById( $projectId ) {
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        if ( !$project ) {
            throw new ApplicationException( "No project with id {$projectId} found", 404 );
        }

        return $project;
    }
}