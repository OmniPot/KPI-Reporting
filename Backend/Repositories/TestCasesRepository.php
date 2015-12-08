<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\InsertQueries;
use KPIReporting\Queries\SelectQueries;
use KPIReporting\Queries\UpdateQueries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;
use PDO;

class TestCasesRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getProjectTestCases( $projectId, $date ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_TEST_CASES );
        $stmt->bindParam( 1, $date, PDO::PARAM_STR );
        $stmt->bindParam( 2, $projectId, PDO::PARAM_INT );

        $result = $stmt->execute();
        if ( !$result ) {
            throw new ApplicationException( $stmt->getErrorInfo(), 400 );
        }

        return $stmt->fetchAll();
    }

    public function getProjectUnallocatedTestCases( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_UNALLOCATED_TEST_CASES );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        return $stmt->fetchAll();
    }

    public function getTestCaseEvents( $testCaseId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_EVENTS );
        $stmt->execute( [ $testCaseId ] );
        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }
        $executions = $stmt->fetchAll();

        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_DAY_CHANGES );
        $stmt->execute( [ $testCaseId ] );
        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }
        $dayChanges = $stmt->fetchAll();

        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_USER_CHANGES );
        $stmt->execute( [ $testCaseId ] );
        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }
        $userChanges = $stmt->fetchAll();

        $events = array_merge( $executions, $dayChanges );
        $events = array_merge( $events, $userChanges );

        return $events;
    }

    public function allocateTestCase( $testCaseId, $userId, $dayId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::ALLOCATE_TEST_CASE );

        $stmt->execute( [ $userId, $dayId, $testCaseId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        if ( $stmt->rowCount() == 0 ) {
            throw new ApplicationException( "Allocation of test case {$testCaseId} failed." );
        }
    }

    public function changeTestCaseStatus( $model, $timestamp, $kpi_accountable, $comment, $configurationId ) {

        $this->beginTran();
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_STATUS_CHANGE );
        $insertData = [
            $timestamp,
            $kpi_accountable,
            $model->userId,
            $model->testCaseId,
            $model->newStatusId,
            $model->oldStatusId,
            $comment,
            $configurationId
        ];
        $stmt->execute( $insertData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_STATUS );
        $updateData = [ $model->newStatusId, $model->testCaseId ];
        $stmt->execute( $updateData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $this->commit();

        return $stmt->rowCount();
    }

    public function changeTestCaseUser( $model, $timestamp, $configurationId ) {

        $this->beginTran();
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_USER_CHANGE );
        $insertData = [
            $timestamp,
            $model->testCaseId,
            $model->oldUserId,
            $model->newUserId,
            $configurationId
        ];
        $stmt->execute( $insertData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_USER );
        $updateData = [ $model->newUserId, $model->externalStatus, $model->testCaseId ];
        $stmt->execute( $updateData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $this->commit();

        return $stmt->rowCount();
    }

    public function changeTestCaseDate( $model, $timestamp, $configurationId ) {

        $this->beginTran();
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_DAY_CHANGE );
        $insertData = [
            $timestamp,
            $model->testCaseId,
            $model->oldDayId,
            $model->newDayId,
            $model->reasonId,
            $configurationId
        ];
        $stmt->execute( $insertData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_DAY );
        $updateData = [ $model->newDayId, $model->externalStatus, $model->testCaseId ];
        $stmt->execute( $updateData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $this->commit();

        return $stmt->rowCount();
    }

    public function clearTestCases( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::CLEAR_TEST_CASES );
        $stmt->execute( [ $projectId ] );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}