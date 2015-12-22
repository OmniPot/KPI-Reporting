<?php

namespace KPIReporting\Controllers;

use KPIReporting\Repositories\StatusesRepository;

class StatusesController {

    /**
     * @authorize
     * @method GET
     * @customRoute('statuses/all')
     */
    public function getAllStatuses() {
        $statuses = StatusesRepository::getInstance()->getAll();

        return $statuses;
    }
}