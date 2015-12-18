<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\CountQueries;
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

    public function getProjectUnallocatedTestCases( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_UNALLOCATED_TEST_CASES );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $unallocated = $stmt->fetchAll();

        return $unallocated;
    }

    public function getProjectExpiredNonFinalTestCases( $projectId, $date ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_EXPIRED_TEST_CASES );

        $stmt->bindParam( 1, $projectId, \PDO::PARAM_INT );
        $stmt->bindParam( 2, $date, \PDO::PARAM_STR );

        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getProjectUnallocatedTestCasesCount( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( CountQueries::GET_PROJECT_UNALLOCATED_TEST_CASES_COUNT );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $unallocated = $stmt->fetch();

        return $unallocated;
    }

    public function getProjectExpiredNonFinalTestCasesCount( $projectId, $date, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( CountQueries::GET_PROJECT_EXPIRED_NON_FINAL_TEST_CASES_COUNT );

        $stmt->execute( [ $projectId, $date, $configId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $result = $stmt->fetch();

        return $result[ 'expiredNonFinalTestCasesCount' ];
    }

    public function getTestCaseEvents( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_EXECUTIONS );
        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
        $executions = $stmt->fetchAll();

        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_DAY_CHANGES );
        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
        $dayChanges = $stmt->fetchAll();

        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TEST_CASE_USER_CHANGES );
        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
        $userChanges = $stmt->fetchAll();

        $events = array_merge( $executions, $dayChanges );
        $events = array_merge( $events, $userChanges );

        return $events;
    }

    public function insertTestCase( $title, $nodeId, $projectId ) {

        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::INSERT_TEST_CASE );

        $stmt->bindParam( 1, $title, \PDO::PARAM_STR );
        $stmt->bindParam( 2, $nodeId, \PDO::PARAM_INT );
        $stmt->bindParam( 3, 1, \PDO::PARAM_INT );
        $stmt->bindParam( 4, $projectId, \PDO::PARAM_INT );
        $stmt->bindParam( 5, 1, \PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        if ( $stmt->rowCount() == 0 ) {
            throw new ApplicationException( "Insertion of test case with node id {$nodeId} failed!" );
        }
    }

    public function markDeleted( $externalId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_EXTERNAL_STATUS );

        $stmt->bindParam( 1, 3, \PDO::PARAM_INT );
        $stmt->bindParam( 2, $externalId, \PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
    }

    public function allocateTestCase( $testCaseId, $userId, $dayId, $statusId = 1 ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::ALLOCATE_TEST_CASE );

        $stmt->execute( [ $userId, $dayId, $statusId, $testCaseId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
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
            $model->newStatus->id,
            $model->oldStatusId,
            $comment,
            $configurationId
        ];
        $stmt->execute( $insertData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $this->commit();

        return $stmt->rowCount();
    }

    public function updateTestCaseStatus( $newStatus, $testCaseId ) {
        $this->beginTran();

        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_STATUS );
        $updateData = [ $newStatus->id, $testCaseId ];
        $stmt->execute( $updateData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
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
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_USER );
        $updateData = [ $model->newUserId, $model->externalStatus, $model->testCaseId ];
        $stmt->execute( $updateData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
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
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::UPDATE_TEST_CASE_DAY );
        $updateData = [ $model->newDayId, $model->externalStatus, $model->testCaseId ];
        $stmt->execute( $updateData );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $this->commit();

        return $stmt->rowCount();
    }

    public function clearRemainingTestCasesOnPlanReset( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::CLEAR_PROJECT_REMAINING_TEST_CASES_ON_RESET );
        $stmt->execute( [ $projectId ] );

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
    }

    public function clearRemainingTestCasesOnDayEnd() {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::CLEAR_EXPIRED_TEST_CASES_ON_DAY_END );
        $stmt->execute();

        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}