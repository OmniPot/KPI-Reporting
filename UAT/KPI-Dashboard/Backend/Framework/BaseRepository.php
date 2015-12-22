<?php

namespace KPIReporting\Framework;

use KPIReporting\Exceptions\ApplicationException;
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

    public function getDatabaseInstance() {
        return $this->databaseInstance;
    }

    public function setDatabaseInstance( $databaseInstance ) {
        $this->databaseInstance = $databaseInstance;
    }

    public function getLastId( $name = null ) {
        return $this->databaseInstance->lastId( $name );
    }

    public function beginTran() {
        $this->databaseInstance->beginTran();
    }

    public function rollback() {
        $this->databaseInstance->rollback();
    }

    public function commit() {
        $this->databaseInstance->commit();
    }
}