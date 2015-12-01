<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ChangeDayBindingModel;
use KPIReporting\BindingModels\ChangeStatusBindingModel;
use KPIReporting\BindingModels\ChangeUserBindingModel;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
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
     * @customRoute('projects/int/testCases/changeStatus');
     * @param $projectId
     * @param ChangeStatusBindingModel $model
     * @return mixed
     * @throws \KPIReporting\Exceptions\ApplicationException
     * @internal param $projectId
     */
    public function changeStatus( $projectId, ChangeStatusBindingModel $model ) {
        $kpi_accountable = 1;
        $comment = '';
        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $executions = TestCasesRepository::getInstance()->changeTestCaseStatus(
            $model,
            $this->getCurrentDateTime(),
            $kpi_accountable,
            $comment,
            $configuration[ 'configId' ]
        );

        return $executions;
    }

    /**
     * @method POST
     * @customRoute('projects/int/testCases/changeUser');
     * @param $projectId
     * @param ChangeUserBindingModel $model
     * @return mixed
     * @throws \KPIReporting\Exceptions\ApplicationException
     * @internal param $projectId
     */
    public function changeUser( $projectId, ChangeUserBindingModel $model ) {
        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $changeResult = TestCasesRepository::getInstance()->changeTestCaseUser(
            $model,
            $this->getCurrentDateTime(),
            $configuration[ 'configId' ]
        );

        return $changeResult;
    }

    /**
     * @method POST
     * @customRoute('projects/int/testCases/changeDate');
     * @param $projectId
     * @param ChangeDayBindingModel $model
     * @return mixed
     * @throws \KPIReporting\Exceptions\ApplicationException
     * @internal param $projectId
     */
    public function changeDate( $projectId, ChangeDayBindingModel $model ) {
        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $changeResult = TestCasesRepository::getInstance()->changeTestCaseDate(
            $model,
            $this->getCurrentDateTime(),
            $configuration[ 'configId' ]
        );

        return $changeResult;
    }
}