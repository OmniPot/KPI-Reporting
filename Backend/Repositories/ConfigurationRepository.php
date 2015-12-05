<?php

namespace KPIReporting\Repositories;

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
        $result = $this->getDatabaseInstance()->prepare( SelectQueries::GET_ACTIVE_CONFIG );
        $result->execute( [ $projectId ] );

        return $result->fetch();
    }

    public function createNewConfiguration( $projectId, $timestamp ) {
        $result = $this->getDatabaseInstance()->prepare( InsertQueries::CREATE_CONFIGURATION );

        return $result->execute( [ $projectId, $timestamp, null, 0 ] );
    }

    public function closeActiveConfiguration( $configId, $timestamp ) {
        $result = $this->getDatabaseInstance()->prepare( UpdateQueries::CLOSE_CONFIGURATION );
        $result->bindParam( 1, $timestamp, PDO::PARAM_STR );
        $result->bindParam( 2, $configId, PDO::PARAM_STR );

        return $result->execute( [ $timestamp, $configId ] );
    }

    public function getProjectAssignedUsers( $projectId ) {
        $configResult = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        $usersQuery = SelectQueries::GET_PROJECT_ASSIGNED_USERS;
        $result = $this->getDatabaseInstance()->prepare( $usersQuery );
        $result->execute( [ $projectId, $configResult[ 'configId' ] ] );

        return $result->fetchAll();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}