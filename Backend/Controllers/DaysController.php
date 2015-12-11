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
     * @method POST
     * @customRoute('projects/int/extendDuration')
     */
    public function extendProjectDuration( $projectId, ExtendDurationBindingModel $model ) {
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $startDate = new \DateTime( $model->startDate );

        SetupRepository::getInstance()->assignDaysToProject(
            $projectId,
            $model->endDuration,
            $model->expectedTestCases,
            $config[ 'configId' ],
            $startDate,
            $model->startDuration
        );
        $allocatedDays = DaysRepository::getInstance()->getProjectAssignedDays( $projectId, $config[ 'configId' ] );

        return $allocatedDays;
    }
}