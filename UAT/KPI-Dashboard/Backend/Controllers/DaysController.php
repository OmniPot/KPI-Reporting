<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\DatDateChangeBindingModel;
use KPIReporting\BindingModels\ExtendDurationBindingModel;
use KPIReporting\BindingModels\StopExecutionBindingModel;
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
        ConfigurationRepository::getInstance()->updateParkedConfigurations();

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
     * @method GET
     * @customRoute('projects/int/availableDates')
     */
    public function getAvailableDays( $projectId ) {
        $projectDays = DaysRepository::getInstance()->getProjectAssignedDays( $projectId );
        $availableDays = DaysRepository::getInstance()->getAvailableDays( $projectDays );

        return $availableDays;
    }

    /**
     * @authorize
     * @method PUT
     * @customRoute('days/int/changeDate')
     */
    public function changeDayDate( $dayId, DatDateChangeBindingModel $model ) {
        DaysRepository::getInstance()->changeDayDate( $dayId, $model->newDate );

        return [ 'msg' => "Successfully changed date for day with Id {$dayId}" ];
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

    /**
     * @authorize
     * @method PUT
     * @customRoute('projects/int/stopExecution')
     */
    public function stopProjectExecution( $projectId, StopExecutionBindingModel $model ) {
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        DaysRepository::getInstance()->stopExecution( $projectId, $model, $config[ 'configId' ] );

        return [ 'msg' => "Project execution stopped for {$model->reason->description}." ];
    }

    /**
     * @authorize
     * @method PUT
     * @customRoute('projects/int/resumeExecution')
     */
    public function resumeProjectExecution( $projectId ) {
        $config = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        DaysRepository::getInstance()->resumeExecution( $projectId, $config[ 'configId' ] );

        return [ 'msg' => "Project execution resumed." ];
    }

    /**
     * @authorize
     * @method DELETE
     * @customRoute('projects/int/days/int/delete')
     */
    public function deleteProjectDay( $projectId, $dayId ) {
        DaysRepository::getInstance()->deleteProjectDay( $projectId, $dayId );

        return [ 'msg' => "Day successfully deleted." ];
    }
}