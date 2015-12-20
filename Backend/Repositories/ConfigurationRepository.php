<?php

namespace KPIReporting\Repositories;

use DateInterval;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;
use KPIReporting\Queries\InsertQueries;
use KPIReporting\Queries\SelectQueries;
use KPIReporting\Queries\UpdateQueries;
use PDO;

class ConfigurationRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getActiveProjectConfiguration( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_ACTIVE_CONFIG );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
        }

        return $stmt->fetch();
    }

    public function getExistingProjectConfiguration( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_EXISTING_CONFIG );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
        }

        $result = $stmt->fetch();

        return $result;
    }

    public function createNewConfiguration( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::CREATE_CONFIGURATION );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
        }

        return $this->getActiveProjectConfiguration( $projectId );
    }

    public function closeActiveConfiguration( $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::CLOSE_CONFIGURATION );
        $stmt->bindParam( 1, $configId, PDO::PARAM_STR );

        $stmt->execute( [ $configId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
        }

        return $stmt->rowCount();
    }

    public function parkConfiguration( $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::STOP_EXECUTION );

        $stmt->execute( [ $configId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
        }

        return $stmt->rowCount();
    }

    public function updateParkedConfigurations() {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PARKED_CONFIGURATIONS );
        $stmt->execute();
        $configs = $stmt->fetchAll();

        $this->beginTran();

        foreach ( $configs as $configK => $configV ) {
            $newDuration = 1;

            $currentDate = new \DateTime( 'now', new \DateTimeZone( 'Asia/Qatar' ) );
            $parkedAt = new \DateTime( $configV[ 'parkedAt' ], new \DateTimeZone( 'Asia/Qatar' ) );
            $formattedCurrent = $currentDate->format( 'Y-m-d' );
            while ( $formattedCurrent != $parkedAt->format( 'Y-m-d' ) ) {
                if ( !SetupRepository::getInstance()->isWeekend( $parkedAt->format( 'Y-m-d' ) ) ) {
                    $newDuration++;
                }

                $parkedAt = $parkedAt->add( new DateInterval( 'P' . 1 . 'D' ) );
            }

            $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_PARKED_CONFIGURATION );
            $stmt->execute( [ $newDuration, $configV[ 'configId' ] ] );

            if ( !$stmt ) {
                $this->rollback();
                throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
            }
        }

        $this->commit();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}