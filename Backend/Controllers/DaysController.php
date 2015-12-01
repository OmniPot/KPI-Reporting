<?php

namespace KPIReporting\Controllers;

use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\DaysRepository;

class DaysController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/remainingDays')
     * @param $projectId
     * @return string
     */
    public function getProjectRemainingDays( $projectId ) {
        $remainingDays = DaysRepository::getInstance()->getProjectRemainingDays( $projectId, $this->getCurrentDate() );

        return $remainingDays;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/suggestedCommitment')
     * @param $projectId
     * @return string
     */
    public function getProjectSuggestedCommitment( $projectId ) {
        $commitment = DaysRepository::getInstance()->getProjectSuggestedCommitment( $projectId );

        return $commitment;
    }
}