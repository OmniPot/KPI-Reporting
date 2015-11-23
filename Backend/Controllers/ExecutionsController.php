<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ExecutionBindingModel;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ExecutionsRepository;

class ExecutionsController extends BaseController {

    /**
     * @method GET
     * @customRoute('projects/int/executions');
     * @param $projectId
     * @return mixed
     */
    public function getProjectExecutions( $projectId ) {
        $executions = ExecutionsRepository::getInstance()->getProjectExecutions( $projectId );

        return $executions;
    }

    /**
     * @method POST
     * @customRoute('testCases/execute');
     * @param ExecutionBindingModel $model
     * @return mixed
     * @internal param $projectId
     */
    public function executeTestCase( ExecutionBindingModel $model ) {
        $timestamp = new \DateTime();
        $timestampFormatted = $timestamp->format( 'Y-m-d H:i:s' );
        $kpi_accountable = 1;
        $comment = '';

        $executions = ExecutionsRepository::getInstance()->executeTestCase( $model, $timestampFormatted, $kpi_accountable, $comment );

        return $executions;
    }
}