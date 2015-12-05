<?php

namespace KPIReporting\Repositories;

use KPIReporting\Framework\BaseRepository;
use KPIReporting\Queries\InsertQueries;
use KPIReporting\Queries\SelectQueries;
use KPIReporting\Queries\UpdateQueries;
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

        return $result->rowCount();
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

    public function assignProjectInitialCommitment( $projectId, $duration ) {
        $insert = UpdateQueries::PROJECT_INITIAL_COMMITMENT;
        $result = $this->getDatabaseInstance()->prepare( $insert );

        return $result->execute( [ $duration, $projectId ] );
    }

    public function assignUserToProject( $projectId, $userId, $load, $performance, $configId ) {
        $insert = InsertQueries::INSERT_INTO_PROJECTS_USERS;
        $result = $this->getDatabaseInstance()->prepare( $insert );

        return $result->execute( [ $projectId, $userId, $load, $performance, $configId ] );
    }

    public function assignDayToProject( $projectId, $index, $date, $testCases, $configId ) {
        $insert = InsertQueries::INSERT_INTO_PROJECT_DAYS;
        $result = $this->getDatabaseInstance()->prepare( $insert );

        $result->bindParam( 1, $projectId, PDO::PARAM_INT );
        $result->bindParam( 2, $index, PDO::PARAM_INT );
        $result->bindParam( 3, $date, PDO::PARAM_STR );
        $result->bindParam( 4, $testCases, PDO::PARAM_INT );
        $result->bindParam( 5, $configId, PDO::PARAM_INT );

        return $result->execute();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}