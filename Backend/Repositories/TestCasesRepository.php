<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\InsertQueries;
use KPIReporting\Queries\SelectQueries;
use KPIReporting\Queries\UpdateQueries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;

class TestCasesRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function changeTestCaseStatus( $model, $timestamp, $kpi_accountable, $comment ) {

        // Insert status change
        $result = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_STATUS_CHANGE );
        $insertData = [
            $timestamp,
            $kpi_accountable,
            $model->userId,
            $model->testCaseId,
            $model->newStatusId,
            $model->oldStatusId,
            $comment
        ];
        $result->execute( $insertData );

        if ( !$result ) {
            throw new ApplicationException( 'Status change insertion failed', 400 );
        }

        // Update test case
        $result = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_STATUS );
        $updateData = [ $model->newStatusId, $model->testCaseId ];
        $result->execute( $updateData );

        if ( !$result ) {
            throw new ApplicationException( 'Test case status update failed', 400 );
        }

        return $insertData;
    }

    public function changeTestCaseUser( $model, $timestamp ) {

        // Insert user change
        $result = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_USER_CHANGE );
        $insertData = [
            $timestamp,
            $model->testCaseId,
            $model->oldUserId,
            $model->newUserId
        ];
        $result->execute( $insertData );

        if ( !$result ) {
            throw new ApplicationException( 'Test case user change insertion failed', 400 );
        }

        // Update test case
        $result = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_USER );
        $updateData = [ $model->newUserId, $model->testCaseId ];
        $result->execute( $updateData );

        if ( !$result ) {
            throw new ApplicationException( 'Test case user update failed', 400 );
        }

        return $insertData;
    }

    public function changeTestCaseDate( $model, $timestamp ) {
        // Insert day change
        $result = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_DAY_CHANGE );
        $insertData = [
            $timestamp,
            $model->testCaseId,
            $model->oldDayId,
            $model->newDayId,
            $model->reasonId
        ];
        $result->execute( $insertData );

        if ( !$result ) {
            throw new ApplicationException( 'Test case day change insertion failed', 400 );
        }

        // Update test case
        $result = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_DAY );
        $updateData = [ $model->newDayId, $model->testCaseId ];
        $result->execute( $updateData );

        if ( !$result ) {
            throw new ApplicationException( 'Test case day update failed', 400 );
        }

        return $insertData;
    }

    public function getTestCaseEvents( $projectId ) {
        $result = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_EXECUTIONS );
        $result->execute( [ $projectId ] );
        $executions = $result->fetchAll();

        $result = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_DAY_CHANGES );
        $result->execute( [ $projectId ] );
        $dayChanges = $result->fetchAll();

        $result = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_USER_CHANGES );
        $result->execute( [ $projectId ] );
        $userChanges = $result->fetchAll();

        $events = array_merge( $executions, $dayChanges );
        $events = array_merge( $events, $userChanges );

        return $events;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}