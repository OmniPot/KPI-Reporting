<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ChangeDayBindingModel;
use KPIReporting\BindingModels\ChangeStatusBindingModel;
use KPIReporting\BindingModels\ChangeUserBindingModel;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\TestCasesRepository;

class TestCasesController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('testCases/int/events');
     * @param $testCaseId
     * @return mixed
     * @internal param $projectId
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
     * @customRoute('testCases/changeStatus');
     * @param ChangeStatusBindingModel $model
     * @return mixed
     * @internal param $projectId
     */
    public function changeStatus( ChangeStatusBindingModel $model ) {
        $kpi_accountable = 1;
        $comment = '';

        $executions = TestCasesRepository::getInstance()->changeTestCaseStatus(
            $model,
            $this->getCurrentDateTime(),
            $kpi_accountable,
            $comment
        );

        return $executions;
    }

    /**
     * @method POST
     * @customRoute('testCases/changeUser');
     * @param ChangeUserBindingModel $model
     * @return mixed
     * @internal param $projectId
     */
    public function changeUser( ChangeUserBindingModel $model ) {
        $changeResult = TestCasesRepository::getInstance()->changeTestCaseUser( $model, $this->getCurrentDateTime() );

        return $changeResult;
    }

    /**
     * @method POST
     * @customRoute('testCases/changeDate');
     * @param ChangeDayBindingModel $model
     * @return mixed
     * @internal param $projectId
     */
    public function changeDate( ChangeDayBindingModel $model ) {
        $changeResult = TestCasesRepository::getInstance()->changeTestCaseDate( $model, $this->getCurrentDateTime() );

        return $changeResult;
    }
}