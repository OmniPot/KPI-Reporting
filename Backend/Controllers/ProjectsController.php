<?php

namespace KPIReporting\Controllers;

use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\ProjectsRepository;

class ProjectsController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int')
     */
    public function getById( $projectId ) {
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        return $project;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/config')
     */
    public function getActiveConfig( $projectId ) {
        $activeConfig = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        return $activeConfig;
    }
}