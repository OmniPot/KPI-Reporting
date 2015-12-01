<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\SetupBindingModel;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ProjectsRepository;
use KPIReporting\Repositories\SetupRepository;
use KPIReporting\Repositories\UserRepository;

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
        $project = SetupRepository::getInstance()->checkIfProjectSourceExists( $projectId );
        if ( !$project ) {
            throw new ApplicationException( "Project: with Id {$projectId} not found in ooredoo_pipeline", 404 );
        }

        $replicated = SetupRepository::getInstance()->checkIfProjectIsReplicated( $projectId );
        if ( $replicated[ 'isReplicated' ] == 0 ) {
            SetupRepository::getInstance()->replicateProject( $projectId );
        }

        return SetupRepository::getInstance()->getProjectConfigurationDetails( $projectId );
    }

    /**
     * @method POST
     * @customRoute('projects/int/setup/save')
     * @param $projectId
     * @param SetupBindingModel $model
     * @return string
     * @throws ApplicationException
     */
    public function saveFirstSetupStage( $projectId, SetupBindingModel $model ) {
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        if ( !$project ) {
            throw new ApplicationException( "Project: with Id {$projectId} not found", 404 );
        }

        $this->assignUsersToProject( $projectId, $model->users );
        $this->assignDaysToProject( $projectId, $model->duration );

        return [ 'message' => "Stage saved successfully!" ];
    }

    private function assignUsersToProject( $projectId, $selectedUsers ) {
        foreach ( $selectedUsers as $userId => $load ) {
            if ( !is_object( $load ) ) {
                continue;
            }

            $userPerformanceIndex = UserRepository::getInstance()->getUserPerformanceIndex( $userId );
            $userPerformanceIndicator = ( $userPerformanceIndex[ 'index' ] / 100 ) * $load->indicator;
            $assignResult = SetupRepository::getInstance()->assignUserToProject(
                $projectId,
                $userId,
                $load->indicator,
                $userPerformanceIndicator
            );

            if ( !$assignResult ) {
                throw new ApplicationException( "Assigning user {$userId} to project {$projectId} failed.", 400 );
            }
        }
    }

    private function assignDaysToProject( $projectId, $days ) {
        foreach ( $days as $index => $date ) {
            $insertResult = SetupRepository::getInstance()->assignDayToProject( $projectId, $index, $date );

            if ( !$insertResult ) {
                throw new ApplicationException( "Assigning day {$index} to project {$projectId} failed.", 400 );
            }
        }
    }
}