<?php

namespace KPIReporting\Repositories;

use KPIReporting\Framework\BaseRepository;
use KPIReporting\Queries\InsertQueries;
use KPIReporting\Queries\SelectQueries;
use PDO;

class SetupRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function checkIfProjectIsReplicated( $projectId ) {
        $checkQuery = SelectQueries::CHECK_IF_PROJECT_IS_REPLICATED;
        $result = $this->getDatabaseInstance()->prepare( $checkQuery );
        $result->execute( [ $projectId ] );

        return $result->fetch();
    }

    public function checkIfProjectSourceExists( $projectId ) {
        $checkQuery = SelectQueries::CHECK_IF_PROJECT_SOURCE_EXISTS;
        $result = $this->getDatabaseInstance()->prepare( $checkQuery );
        $result->execute( [ $projectId ] );

        return $result->fetch();
    }

    public function replicateProject( $projectId ) {
        $checkQuery = InsertQueries::REPLICATE_PROJECT;
        $result = $this->getDatabaseInstance()->prepare( $checkQuery );
        $result->execute( [ $projectId ] );

        return $result->fetch();
    }

    public function getProjectConfigurationDetails( $projectId ) {
        $usersQuery = SelectQueries::GET_PROJECT_CONFIG_DETAILS;
        $result = $this->getDatabaseInstance()->prepare( $usersQuery );
        $result->execute( [ $projectId ] );

        return $result->fetchAll();
    }

    public function assignUserToProject( $projectId, $userId, $loadIndicator, $performanceIndicator ) {
        $insert = InsertQueries::INSERT_INTO_PROJECTS_USERS;
        $result = $this->getDatabaseInstance()->prepare( $insert );
        $result->execute( [ $projectId, $userId, $loadIndicator, $performanceIndicator ] );

        return $result->rowCount();
    }

    public function assignDayToProject( $projectId, $index, $date ) {
        $insert = InsertQueries::INSERT_INTO_DAYS;
        $result = $this->getDatabaseInstance()->prepare( $insert );

        $result->bindParam( 1, $projectId, PDO::PARAM_INT );
        $result->bindParam( 2, $index, PDO::PARAM_INT );
        $result->bindParam( 3, $date, PDO::PARAM_STR );

        return $result->execute();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}