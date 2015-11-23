<?php

namespace KPIReporting\Repositories;

use KPIReporting\Config\Queries;
use KPIReporting\Framework\BaseRepository;

class StatusesRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getAll() {
        $result = $this->getDatabaseInstance()->prepare( Queries::ALL_STATUSES );
        $result->execute();
        $statuses = $result->fetchAll();

        return $statuses;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}