<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\SelectQueries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;
use PDO;

class DaysRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getProjectRemainingDays( $projectId, $currentDate, $configId ) {
        $result = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_REMAINING_DAYS );

        $result->bindParam( 1, $projectId, PDO::PARAM_INT );
        $result->bindParam( 2, $currentDate, PDO::PARAM_STR );
        $result->bindParam( 3, $configId, PDO::PARAM_INT );
        $result->execute();

        if ( !$result ) {
            throw new ApplicationException( 'Error retrieving remaining project days', 400 );
        }

        $remainingDays = $result->fetchAll();

        return $remainingDays;
    }

    public function getProjectAssignedDays( $projectId, $configId ) {
        $result = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_ASSIGNED_DAYS );
        $result->execute( [ $projectId, $configId ] );

        return $result->fetchAll();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}