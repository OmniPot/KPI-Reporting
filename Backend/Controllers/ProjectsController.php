<?php

namespace KPIReporting\Controllers;

use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\ProjectsRepository;
use KPIReporting\Repositories\TestCasesRepository;

class ProjectsController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int')
     */
    public function getById( $projectId ) {
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        $unallocated = TestCasesRepository::getInstance()->getProjectUnallocatedTestCasesCount( $projectId );
        $project[ 'unAllocatedTestCasesCount' ] = $unallocated[ 'unAllocatedTestCasesCount' ];

        return $project;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/config')
     */
    public function getActiveConfig( $projectId ) {
        TestCasesRepository::getInstance()->clearRemainingTestCasesOnDayEnd();
        ConfigurationRepository::getInstance()->updateParkedConfigurations();

        $activeConfig = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        return $activeConfig;
    }

    /**
     * @method GET
     * @customRoute('projects/int/sync')
     */
    public function syncProjectTestCases( $projectId ) {
        $result = ProjectsRepository::getInstance()->syncProjectTestCases( $projectId );

        return $result;
    }
}