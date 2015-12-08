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
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_BY_ID );

        $result = $stmt->execute( [ $projectId ] );

        if ( !$result ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        return $stmt->fetch();
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

    public function getProjectDurations( $projectId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_DURATIONS );
        $stmt->execute( [ $configId, $projectId ] );

        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        return $stmt->fetch();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}