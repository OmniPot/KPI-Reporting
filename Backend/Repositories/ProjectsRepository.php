<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\SelectQueries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;
use PDO;

class ProjectsRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getProjectById( $projectId ) {
        $projectQuery = SelectQueries::GET_PROJECT_BY_ID;
        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
        $result->execute( [ $projectId ] );

        return $result->fetch();
    }

    public function getProjectTestCases( $projectId, $timestamp ) {
        $projectTestCases = SelectQueries::GET_PROJECT_TEST_CASES;
        $result = $this->getDatabaseInstance()->prepare( $projectTestCases );
        $result->bindParam( 1, $timestamp, PDO::PARAM_STR );
        $result->bindParam( 2, $projectId, PDO::PARAM_INT );
        $result->execute();

        return $result->fetchAll();
    }

    public function getProjectInitialCommitment( $projectId ) {
        $projectQuery = SelectQueries::GET_PROJECT_INITIAL_COMMITMENT;
        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
        $result->execute( [ $projectId ] );

        return $result->fetch();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}