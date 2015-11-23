<?php

namespace KPIReporting\Controllers;

use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\AllocationsRepository;

class AllocationsController extends BaseController {

    /**
     * @method GET
     * @customRoute('projects/int/allocationMap')
     * @param $projectId
     * @return array
     */
    public function getProjectAllocationMap( $projectId ) {
        $testCases = AllocationsRepository::getInstance()->getProjectAllocations( $projectId, 0, 10 );

        return $testCases;
    }
}