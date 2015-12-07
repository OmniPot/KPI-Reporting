<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\SetupBindingModel;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\ProjectsRepository;
use KPIReporting\Repositories\SetupRepository;

class SetupController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/setup')
     * @param int $projectId
     * @return string
     * @throws ApplicationException
     */
    public function getProjectSetupPage( $projectId ) {
        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $project = SetupRepository::getInstance()->checkIfProjectSourceExists( $projectId );
        if ( !$project ) {
            throw new ApplicationException( "Project with Id {$projectId} not found in ooredoo_pipeline", 404 );
        }

        $replicated = SetupRepository::getInstance()->checkIfProjectIsReplicated( $projectId );
        if ( !$replicated ) {
            SetupRepository::getInstance()->replicateProject( $projectId );
        }

        $details = [ ];
        $details[ 'activeUsers' ] = ConfigurationRepository::getInstance()->getProjectAssignedUsers( $projectId );
        $details[ 'durations' ] = ProjectsRepository::getInstance()->getProjectDurations( $projectId, $configuration[ 'configId' ] );

        return $details;
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/int/setup/save')
     * @param $projectId
     * @param SetupBindingModel $model
     * @return string
     * @throws ApplicationException
     */
    public function saveProjectSetup( $projectId, SetupBindingModel $model ) {
        SetupRepository::getInstance()->saveProjectSetup(
            $projectId,
            $model,
            $this->getCurrentDateTime(),
            $this->getCurrentDateObject()
        );
    }

}