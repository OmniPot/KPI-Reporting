<?php

namespace KPIReporting\Controllers;

use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\DaysRepository;
use KPIReporting\Repositories\SetupRepository;

class DaysController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/remainingDays')
     * @param $projectId
     * @return string
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
     * @param $projectId
     * @return string
     */
    public function getProjectAllocatedDaysPage( $projectId ) {
        $activeConfig = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $activeUsers = ConfigurationRepository::getInstance()->getProjectAssignedUsers( $projectId );
        $allocatedDays = DaysRepository::getInstance()->getProjectAssignedDays( $projectId, $activeConfig[ 'configId' ] );

        return [
            'config' => $activeConfig, 'activeUsers' => $activeUsers, 'allocatedDays' => $allocatedDays
        ];
    }
}