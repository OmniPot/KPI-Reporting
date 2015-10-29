<?php

namespace Medieval\Framework;

use Medieval\Framework\Config\DatabaseConfig;
use Medieval\Framework\Database\Database;

class BaseRepository {

    /** @var $database Database */
    protected $databaseInstance;

    protected function __construct() {
        $this->setDatabaseInstance(
            Database::getInstance( DatabaseConfig::DB_INSTANCE_NAME )
        );
    }

    public function getLoggedUserInfo() {
        $query = "SELECT * FROM users WHERE id = ?";

        $result = $this->databaseInstance->prepare( $query );
        $result->execute( [ $_SESSION[ 'id' ] ] );

        return $result->fetch();
    }

    public function getDatabaseInstance() {
        return $this->databaseInstance;
    }

    public function setDatabaseInstance( $databaseInstance ) {
        $this->databaseInstance = $databaseInstance;
    }

    public function getProjectById( $projectId ) {

        $projectQuery = "
            SELECT
                p.id,
                p.name,
                p.description,
                p.duration,
                p.start_date,
                p.end_date
            FROM
                Projects p
            WHERE p.id = ?";

        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
        $result->execute( [ $projectId ] );

        $project = $result->fetch();

        return $project;
    }
}