<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ChangeDayBindingModel;
use KPIReporting\BindingModels\ChangeStatusBindingModel;
use KPIReporting\BindingModels\ChangeUserBindingModel;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\ProjectsRepository;
use KPIReporting\Repositories\TestCasesRepository;

class TestCasesController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/testCases');
     */
    public function getProjectTestCases( $projectId ) {
        ProjectsRepository::getInstance()->syncProjectTestCases( $projectId );

        $testCases = ProjectsRepository::getInstance()->getProjectTestCasesForAllocationMap( $projectId );

        return $testCases;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('testCases/int/events');
     */
    public function getTestCaseEvents( $testCaseId ) {
        $events = TestCasesRepository::getInstance()->getTestCaseEvents( $testCaseId );

        usort(
            $events,
            function ( $e1, $e2 ) {
                return $e1[ 'timestamp' ] > $e2[ 'timestamp' ] ? 1 : 0;
            }
        );

        return $events;
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/int/testCases/changeStatus');
     */
    public function changeStatus( $projectId, ChangeStatusBindingModel $model ) {
        $kpi_accountable = 1;
        $comment = '';
        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        if ( $model->newStatus->isBlocked == 0 ) {
            TestCasesRepository::getInstance()->changeTestCaseStatus( $model, $kpi_accountable, $comment, $configuration[ 'configId' ] );
        }

        TestCasesRepository::getInstance()->updateTestCaseStatus( $model->newStatus, $model->testCaseId );

        return [ 'msg' => 'Test case status updated successfully' ];
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/int/testCases/changeUser');
     */
    public function changeUser( $projectId, ChangeUserBindingModel $model ) {
        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $changeResult = TestCasesRepository::getInstance()->changeTestCaseUser( $model, $configuration[ 'configId' ] );

        return $changeResult;
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/int/testCases/changeDate');
     */
    public function changeDate( $projectId, ChangeDayBindingModel $model ) {
        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $changeResult = TestCasesRepository::getInstance()->changeTestCaseDate( $model, $configuration[ 'configId' ] );

        return $changeResult;
    }
}