<?php

namespace KPIReporting\Repositories;

use DateInterval;
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

    public function getProjectRemainingDays( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_REMAINING_DAYS );

        $stmt->bindParam( 1, $projectId, PDO::PARAM_INT );

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

    public function getLastConfigReset( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_LAST_PROJECT_CONFIG_RESET );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $result = $stmt->fetch();

        return $result[ 'lastConfigReset' ];
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

    public function getAvailableDays( $projectDays ) {
        $date = new \DateTime( 'now', new \DateTimeZone( 'Asia/Qatar' ) );
        $availableDays = [ ];

        while ( count( $availableDays ) < 30 ) {
            $taken = false;
            foreach ( $projectDays as $key => $value ) {
                if ( $value[ 'dayDate' ] == $date->format( 'Y-m-d' ) ) {
                    $taken = true;
                    break;
                }
            }

            if ( !$taken ) {
                $availableDays[] = $date->format( 'Y-m-d' );
            }

            $date = $date->add( new DateInterval( 'P' . 1 . 'D' ) );
        }

        return $availableDays;
    }

    public function changeDayDate( $dayId, $newDate ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_DAY_DATE );

        $stmt->bindParam( 1, $newDate, \PDO::PARAM_STR );
        $stmt->bindParam( 2, $dayId, \PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            $this->rollback();
            throw new ApplicationException( "Day update failed", 400 );
        }
    }

    public function insertPlanChange( $duration, $extensionKey, $explanation, $projectId, $reasonId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_PLAN_CHANGE );

        $stmt->bindParam( 1, $duration, \PDO::PARAM_INT );
        $stmt->bindParam( 2, $extensionKey, \PDO::PARAM_INT );
        $stmt->bindParam( 3, $explanation, \PDO::PARAM_STR );
        $stmt->bindParam( 4, $projectId, \PDO::PARAM_INT );
        $stmt->bindParam( 5, $reasonId, \PDO::PARAM_INT );
        $stmt->bindParam( 6, $configId, \PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            $this->rollback();
            throw new ApplicationException( "Insertion of plan change failed for reason with Id {$reasonId}", 400 );
        }
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

    public function extendProjectDuration( $projectId, $model, $expectedTCPD, $config ) {
        $this->beginTran();
        $extensionKey = DaysRepository::getInstance()->getNextExtensionKey();

        foreach ( $model->extensionReasons as $reasonK => $reasonV ) {
            if ( is_object( $reasonV ) ) {
                DaysRepository::getInstance()->insertPlanChange(
                    $reasonV->duration,
                    $extensionKey,
                    null,
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

    public function overrideProjectConfiguration( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::OVERRIDE_PROJECT_CONFIGURATION );

        $stmt->execute( [ $projectId ] );

        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->rowCount();
    }

    public function clearRemainingDays( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( DeleteQueries::DELETE_PROJECT_REMAINING_DAYS );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
    }

    public function deleteProjectDay( $projectId, $dayId ) {
        $this->beginTran();
        $stmt = $this->getDatabaseInstance()->prepare( DeleteQueries::DELETE_PROJECT_DAY );

        $stmt->execute( [ $projectId, $dayId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            throw new ApplicationException( "Deletion failed due to previous actions taken for that day.", 400 );
        }

        $this->commit();
    }

    public function stopExecution( $projectId, $model, $configId ) {
        $this->beginTran();

        $this->insertPlanChange( null, null, null, $projectId, $model->reason->id, $configId );
        TestCasesRepository::getInstance()->clearRemainingTestCases( $projectId );
        $this->clearRemainingDays( $projectId );
        ConfigurationRepository::getInstance()->parkConfiguration( $configId );

        $this->commit();
    }

    public function resumeExecution( $projectId, $configId ) {
        $this->beginTran();

        $configRepo = ConfigurationRepository::getInstance();
        $activeUsers = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $configId );
        $configRepo->closeActiveConfiguration( $configId );
        $newConfig = $configRepo->createNewConfiguration( $projectId );
        SetupRepository::getInstance()->assignUsersToProject( $projectId, $activeUsers, $newConfig[ 'configId' ] );

        $this->commit();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}