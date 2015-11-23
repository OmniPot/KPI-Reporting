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

    public function getLastId( $name = null ) {
        return $this->databaseInstance->lastId( $name );
    }

    public function getDatabaseInstance() {
        return $this->databaseInstance;
    }

    public function setDatabaseInstance( $databaseInstance ) {
        $this->databaseInstance = $databaseInstance;
    }

    protected function checkForExistingProject( $projectId ) {
        $projectQuery = "SELECT p.id FROM projects p WHERE p.id = ?";
        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
        $result->execute( [ $projectId ] );

        if ( !$result->rowCount() ) {
            throw new ApplicationException( "No project with ID: {$projectId} found", 404 );
        }
    }
}