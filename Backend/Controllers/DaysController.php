<?php

namespace KPIReporting\Controllers;

use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\DaysRepository;
use KPIReporting\Repositories\ProjectsRepository;

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
        $activeUsers = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $config[ 'configId' ] );
        $allocatedDays = DaysRepository::getInstance()->getProjectAssignedDays( $projectId, $config[ 'configId' ] );

        return [
            'config' => $config, 'activeUsers' => $activeUsers, 'allocatedDays' => $allocatedDays
        ];
    }
}