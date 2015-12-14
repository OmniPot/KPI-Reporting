<?php

namespace KPIReporting\Repositories;

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

    public function createNewConfiguration( $projectId, $timestamp ) {
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::CREATE_CONFIGURATION );

        $stmt->execute( [ $projectId, $timestamp, null, 0 ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
        }

        return $this->getActiveProjectConfiguration( $projectId );
    }

    public function closeActiveConfiguration( $configId, $timestamp ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::CLOSE_CONFIGURATION );
        $stmt->bindParam( 1, $timestamp, PDO::PARAM_STR );
        $stmt->bindParam( 2, $configId, PDO::PARAM_STR );

        $stmt->execute( [ $timestamp, $configId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
        }

        return $stmt->rowCount();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}