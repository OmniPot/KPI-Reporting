<?php

namespace KPIReporting\Repositories;

use KPIReporting\Framework\BaseRepository;
use KPIReporting\Queries\SelectQueries;

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

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}