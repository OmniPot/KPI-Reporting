<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\DeleteQueries;
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

    public function getProjectAssignedDays( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_ASSIGNED_DAYS );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getLastProjectDay( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_LAST_PROJECT_DAY );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetch();
    }

    public function getExtensionReasons() {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_EXTENSION_REASONS );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getResetReasons() {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_RESET_REASONS );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getParkReasons() {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PARK_REASONS );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function insertPlanChange( $timestamp, $duration, $extensionKey, $explanation, $projectId, $reasonId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_PLAN_CHANGE );

        $stmt->bindParam( 1, $timestamp, \PDO::PARAM_STR );
        $stmt->bindParam( 2, $duration, \PDO::PARAM_INT );
        $stmt->bindParam( 3, $extensionKey, \PDO::PARAM_INT );
        $stmt->bindParam( 4, $explanation, \PDO::PARAM_STR );
        $stmt->bindParam( 5, $projectId, \PDO::PARAM_INT );
        $stmt->bindParam( 6, $reasonId, \PDO::PARAM_INT );
        $stmt->bindParam( 7, $configId, \PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            throw new ApplicationException( "Insertion of test plan change failed for reason with Id {$reasonId}", 400 );
        }
    }

    public function extendProjectDuration( $projectId, $model, $expectedTCPD, $config, $time ) {
        $this->beginTran();
        $extensionKey = DaysRepository::getInstance()->getNextExtensionKey();

        foreach ( $model->extensionReasons as $reasonK => $reasonV ) {
            $reason = isset( $reasonV->duration ) ? $reasonV->duration : null;
            $explanation = isset( $reasonV->explanation ) ? $reasonV->explanation : null;

            if ( is_object( $reasonV ) ) {
                DaysRepository::getInstance()->insertPlanChange(
                    $time,
                    $reason,
                    $extensionKey,
                    $explanation,
                    $projectId,
                    $reasonV->id,
                    $config[ 'configId' ]
                );
            }
        }

        SetupRepository::getInstance()->assignDaysToProject(
            $projectId,
            $model->duration,
            $expectedTCPD,
            $config[ 'configId' ],
            $extensionKey
        );

        $this->commit();
    }

    public function overrideProjectConfiguration( $projectId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::OVERRIDE_PROJECT_CONFIGURATION );

        $stmt->execute( [ $projectId, $configId ] );

        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->rowCount();
    }

    public function getNextExtensionKey() {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_NEXT_EXTENSION_KEY );

        $stmt->execute();

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $result = $stmt->fetch();

        return $result[ 'nextExtensionKey' ];
    }

    public function assignDayToProject( $projectId, $index, $date, $expectedTestCases, $extensionKey, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_INTO_PROJECT_DAYS );

        $stmt->bindParam( 1, $projectId, PDO::PARAM_INT );
        $stmt->bindParam( 2, $index, PDO::PARAM_INT );
        $stmt->bindParam( 3, $date, PDO::PARAM_STR );
        $stmt->bindParam( 4, $expectedTestCases, PDO::PARAM_INT );
        $stmt->bindParam( 5, $extensionKey, PDO::PARAM_INT );
        $stmt->bindParam( 6, $configId, PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            $this->rollback();
            throw new ApplicationException( "Assigning day with index {$index} to project with Id {$projectId} failed.", 400 );
        }
    }

    public function clearRemainingDaysOnPlanReset( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( DeleteQueries::DELETE_PROJECT_REMAINING_DAYS_ON_PLAN_RESET );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}