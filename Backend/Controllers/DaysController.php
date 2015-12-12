<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ExtendDurationBindingModel;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\DaysRepository;
use KPIReporting\Repositories\ProjectsRepository;
use KPIReporting\Repositories\SetupRepository;

class DaysController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/remainingDays')
     */
    public function getProjectRemainingDays( $projectId ) {
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $days = DaysRepository::getInstance()->getProjectRemainingDays( $projectId, $this->getCurrentDate(), $config[ 'configId' ] );

        return $days;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/allocatedDays')
     */
    public function getProjectAllocatedDaysPage( $projectId ) {
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $allocatedDays = DaysRepository::getInstance()->getProjectAssignedDays( $projectId, $config[ 'configId' ] );
        $activeUsers = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $config[ 'configId' ] );

        return [ 'activeUsers' => $activeUsers, 'allocatedDays' => $allocatedDays ];
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('extensionReasons')
     */
    public function getExtensionReasons() {
        $reasons = DaysRepository::getInstance()->getExtensionReasons();

        return $reasons;
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/int/extendDuration')
     */
    public function extendProjectDuration( $projectId, ExtendDurationBindingModel $model ) {
        $setupRepo = SetupRepository::getInstance();
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $activeUsers = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $config[ 'configId' ] );

        $dateObject = $this->getCurrentDateObject();
        $time = $this->getCurrentDateTime();
        $date = $this->getCurrentDate();

        foreach ( $model->extensionReasons as $reasonK => $reasonV ) {
            DaysRepository::getInstance()->insertPlanChange( $projectId, $time, $model->planRenew, $reasonV->id, $config[ 'configId' ] );
        }

        if ( $model->planRenew == 1 ) {
            $setupRepo->renewSetup( $projectId, $model->endDuration, $activeUsers, $model->algorithm, $time, $date, $dateObject, $config );
        } else {
            $startDate = new \DateTime( $model->startDate );
            $setupRepo->assignDaysToProject(
                $projectId,
                $model->endDuration,
                $model->expectedTestCases,
                $config[ 'configId' ],
                $startDate,
                $model->startDuration
            );
        }
    }
}