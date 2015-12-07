<?php

namespace KPIReporting\Repositories;

use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Queries\SelectQueries;
use KPIReporting\Framework\BaseRepository;

class StatusesRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getAll() {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_ALL_STATUSES );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        return $stmt->fetchAll();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}