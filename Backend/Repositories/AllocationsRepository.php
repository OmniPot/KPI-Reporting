<?php

namespace KPIReporting\Repositories;

use KPIReporting\Config\Queries;
use KPIReporting\Framework\BaseRepository;

class AllocationsRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getProjectAllocations( $projectId, $skip, $count ) {
        $this->checkForExistingProject( $projectId );

        $allocationsQuery = Queries::PROJECT_ALLOCATIONS;
        $result = $this->getDatabaseInstance()->prepare( $allocationsQuery );
        $result->execute( [ $projectId ] );
        $executions = $result->fetchAll();

        return $executions;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}