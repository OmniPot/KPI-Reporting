<?php

namespace KPIReporting\Repositories;

use DateTime;
use KPIReporting\Config\Queries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;

class ExecutionsRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getProjectExecutionsByDate() {
        $today = new DateTime();
        $todayFormatted = $today->format( 'Y-m-d' );

        $executionsQuery = Queries::EXECUTIONS_BY_DATE;
        $result = $this->getDatabaseInstance()->prepare( $executionsQuery );
        $result->execute( [ $todayFormatted . '%' ] );
        $executions = $result->fetchAll();

        if ( !$executions ) {
            return [ ];
        }

        return $executions;
    }

    public function getProjectExecutions( $projectId ) {
        $this->checkForExistingProject( $projectId );

        $executionsQuery = Queries::PROJECT_EXECUTIONS;
        $result = $this->getDatabaseInstance()->prepare( $executionsQuery );
        $result->execute( [ $projectId ] );
        $executions = $result->fetchAll();

        return $executions;
    }

    public function executeTestCase( $executionModel, $timestamp, $kpi_accountable, $comment ) {

        // Insert execution
        $result = $this->getDatabaseInstance()->prepare( Queries::INSERT_EXECUTION );
        $insertQueryData = [
            $timestamp,
            $kpi_accountable,
            $executionModel->userId,
            $executionModel->testCaseId,
            $executionModel->newStatusId,
            $executionModel->oldStatusId,
            $comment
        ];
        $result->execute( $insertQueryData );

        if ( !$result ) {
            throw new ApplicationException( 'Execution insertion failed', 400 );
        }

        // Update test case
        $result = $this->getDatabaseInstance()->prepare( Queries::TEST_CASE_STATUS_UPDATE );
        $updateQueryData = [ $executionModel->newStatusId, $executionModel->testCaseId ];
        $result->execute( $updateQueryData );

        if ( !$result ) {
            throw new ApplicationException( 'Test case update failed', 400 );
        }

        return $insertQueryData;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}