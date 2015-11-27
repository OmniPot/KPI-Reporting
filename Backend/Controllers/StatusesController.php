<?php

namespace KPIReporting\Controllers;

use KPIReporting\Repositories\StatusesRepository;

class StatusesController {

    /**
     * @authorize
     * @method GET
     * @customRoute('statuses/all')
     * @return array
     * @internal param $testCaseId
     */
    public function getAllStatuses() {
        $statuses = StatusesRepository::getInstance()->getAll();

        return $statuses;
    }
}