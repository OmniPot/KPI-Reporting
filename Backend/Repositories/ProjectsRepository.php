<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\CountQueries;
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
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_BY_ID );
        $result = $stmt->execute( [ $projectId ] );
        if ( !$result ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $project = $stmt->fetch();

        $stmt = $this->getDatabaseInstance()->prepare( CountQueries::GET_PROJECT_UNALLOCATED_TEST_CASES_COUNT );
        $result = $stmt->execute( [ $projectId ] );
        if ( !$result ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $testCases = $stmt->fetch();
        $project[ 'unAllocatedTestCasesCount' ] = $testCases[ 'unAllocatedTestCasesCount' ];

        return $project;
    }

    public function getProjectTestCases( $projectId, $timestamp ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_TEST_CASES );
        $stmt->bindParam( 1, $timestamp, PDO::PARAM_STR );
        $stmt->bindParam( 2, $projectId, PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        return $stmt->fetchAll();
    }

    public function getProjectAssignedUsers( $projectId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_ASSIGNED_USERS );

        $stmt->execute( [ $projectId, $configId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo(), 400 );
        }

        return $stmt->fetchAll();
    }

    public function getProjectCurrentDuration( $projectId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( CountQueries::GET_PROJECT_CURRENT_DURATION );
        $stmt->execute( [ $projectId, $configId ] );

        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $duration = $stmt->fetch();

        return $duration[ 'currentDuration' ];
    }

    public function getProjectInitialCommitment( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_INITIAL_COMMITMENT );
        $stmt->execute( [ $projectId ] );

        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $duration = $stmt->fetch();

        return $duration[ 'initialCommitment' ];
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}