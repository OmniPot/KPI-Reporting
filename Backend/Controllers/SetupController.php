<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ResetBindingModel;
use KPIReporting\BindingModels\SetupBindingModel;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\ProjectsRepository;
use KPIReporting\Repositories\SetupRepository;
use KPIReporting\Repositories\TestCasesRepository;

class SetupController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/setup')
     */
    public function getProjectSetupPage( $projectId ) {
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        if ( !isset( $project[ 'id' ] ) ) {
            $replicated = SetupRepository::getInstance()->replicateProject( $projectId );
            if ( $replicated == 0 ) {
                throw new ApplicationException( "Project with Id {$projectId} failed to replicate", 404 );
            }

            $project = ProjectsRepository::getInstance()->getProjectById( $projectId );
        }

        ProjectsRepository::getInstance()->syncProjectTestCases( $projectId );
        TestCasesRepository::getInstance()->clearRemainingTestCasesOnDayEnd();
        $testCases = TestCasesRepository::getInstance()->getProjectUnallocatedTestCasesCount( $projectId );

        $project[ 'unAllocatedTestCasesCount' ] = $testCases[ 'unAllocatedTestCasesCount' ];
        $project[ 'activeUsers' ] = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $config[ 'configId' ] );
        $project[ 'currentDuration' ] = ProjectsRepository::getInstance()->getProjectCurrentDuration( $projectId, $config[ 'configId' ] );
        $project[ 'expiredNonFinalTestCasesCount' ] = TestCasesRepository::getInstance()->getProjectExpiredNonFinalTestCasesCount(
            $projectId,
            $config[ 'configId' ]
        );

        return $project;
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/int/setup/save')
     */
    public function saveProjectSetup( $projectId, SetupBindingModel $model ) {
        SetupRepository::getInstance()->saveProjectSetup( $projectId, $model, $this->getCurrentDate() );

        return [ 'msg' => "Configuration successfully saved for project with Id {$projectId}!" ];
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/int/setup/clear')
     */
    public function clearProjectSetup( $projectId, ResetBindingModel $model ) {
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        SetupRepository::getInstance()->clearSetup( $projectId, $config, $model->reason );

        return [ 'msg' => "Configuration reset successful for project with Id {$projectId}!" ];
    }
}