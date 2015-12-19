<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ExtendDurationBindingModel;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\DaysRepository;
use KPIReporting\Repositories\ProjectsRepository;
use KPIReporting\Repositories\SetupRepository;
use KPIReporting\Repositories\TestCasesRepository;

class DaysController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/remainingDays')
     */
    public function getProjectRemainingDays( $projectId ) {
        $days = DaysRepository::getInstance()->getProjectRemainingDays( $projectId );

        return $days;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/allocatedDays')
     */
    public function getProjectAllocatedDaysPage( $projectId ) {
        ProjectsRepository::getInstance()->syncProjectTestCases( $projectId );
        TestCasesRepository::getInstance()->clearRemainingTestCasesOnDayEnd();

        $allocatedDays = DaysRepository::getInstance()->getProjectAssignedDays( $projectId );

        return [ 'allocatedDays' => $allocatedDays ];
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
     * @method GET
     * @customRoute('resetReasons')
     */
    public function getResetReasons() {
        $reasons = DaysRepository::getInstance()->getResetReasons();

        return $reasons;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('parkReasons')
     */
    public function getParkReasons() {
        $reasons = DaysRepository::getInstance()->getParkReasons();

        return $reasons;
    }

    /**
     * @authorize
     * @method PUT
     * @customRoute('projects/int/extendDuration')
     */
    public function extendProjectDuration( $projectId, ExtendDurationBindingModel $model ) {
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $activeUsers = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $config[ 'configId' ] );
        $expectedTCPD = SetupRepository::getInstance()->calcExpectedTCPD( $activeUsers );

        DaysRepository::getInstance()->extendProjectDuration( $projectId, $model, $expectedTCPD, $config );

        return [ 'msg' => "Project with Id {$projectId} extended successfully!" ];
    }

    /**
     * @authorize
     * @method PUT
     * @customRoute('projects/int/overrideConfiguration')
     */
    public function overrideProjectConfiguration( $projectId ) {
        $overriddenCount = DaysRepository::getInstance()->overrideProjectConfiguration( $projectId );

        return [ 'msg' => "{$overriddenCount} days updated for project with Id {$projectId}" ];
    }
}