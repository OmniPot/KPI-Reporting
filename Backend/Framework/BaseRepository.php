<?php

namespace KPIReporting\Framework;

use KPIReporting\Framework\Config\DatabaseConfig;
use KPIReporting\Framework\Database\Database;

class BaseRepository {

    /** @var $database Database */
    protected $databaseInstance;

    protected function __construct() {
        $this->setDatabaseInstance(
            Database::getInstance( DatabaseConfig::DB_INSTANCE_NAME )
        );
    }

    public function getLastId( $name = null ) {
        return $this->databaseInstance->lastId( $name );
    }

    public function getDatabaseInstance() {
        return $this->databaseInstance;
    }

    public function setDatabaseInstance( $databaseInstance ) {
        $this->databaseInstance = $databaseInstance;
    }
}