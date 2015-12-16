<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\InsertQueries;
use KPIReporting\Queries\SelectQueries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;
use KPIReporting\Queries\UpdateQueries;
use PDO;

class DaysRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getProjectRemainingDays( $projectId, $currentDate, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_REMAINING_DAYS );

        $stmt->bindParam( 1, $projectId, PDO::PARAM_INT );
        $stmt->bindParam( 2, $currentDate, PDO::PARAM_STR );
        $stmt->bindParam( 3, $configId, PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getProjectAssignedDays( $projectId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_ASSIGNED_DAYS );

        $stmt->execute( [ $projectId, $configId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getExtensionReasons() {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_EXTENSION_REASONS );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function insertPlanChange( $projectId, $timestamp, $type, $duration, $reasonId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_PLAN_CHANGE );

        $stmt->bindParam( 1, $timestamp, \PDO::PARAM_STR );
        $stmt->bindParam( 3, $type, \PDO::PARAM_INT );
        $stmt->bindParam( 2, $duration, \PDO::PARAM_INT );
        $stmt->bindParam( 4, $projectId, \PDO::PARAM_INT );
        $stmt->bindParam( 5, $reasonId, \PDO::PARAM_INT );
        $stmt->bindParam( 6, $configId, \PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            throw new ApplicationException( "Insertion of test plan change failed for reason with Id {$reasonId}", 400 );
        }
    }

    public function overrideProjectConfiguration( $projectId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::OVERRIDE_PROJECT_CONFIGURATION );

        $stmt->execute( [ $projectId, $configId ] );

        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->rowCount();
    }

    public function assignDayToProject( $projectId, $index, $date, $expectedTestCases, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_INTO_PROJECT_DAYS );

        $stmt->bindParam( 1, $projectId, PDO::PARAM_INT );
        $stmt->bindParam( 2, $index, PDO::PARAM_INT );
        $stmt->bindParam( 3, $date, PDO::PARAM_STR );
        $stmt->bindParam( 4, $expectedTestCases, PDO::PARAM_INT );
        $stmt->bindParam( 5, $configId, PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            $this->rollback();
            throw new ApplicationException( "Assigning day with Id {$index} to project with Id {$projectId} failed.", 400 );
        }
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}