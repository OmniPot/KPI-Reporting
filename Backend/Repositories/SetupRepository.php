<?php

namespace KPIReporting\Repositories;

use DateInterval;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;
use KPIReporting\Queries\InsertQueries;
use KPIReporting\Queries\SelectQueries;
use KPIReporting\Queries\UpdateQueries;

class SetupRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function checkIfProjectIsReplicated( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::CHECK_IF_PROJECT_IS_REPLICATED );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        return $stmt->fetch();
    }

    public function checkIfProjectSourceExists( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::CHECK_IF_PROJECT_SOURCE_EXISTS );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        return $stmt->fetch();
    }

    public function replicateProject( $projectId ) {
        $this->beginTran();
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::REPLICATE_PROJECT );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $this->commit();

        return $stmt->rowCount();
    }

    public function saveProjectSetup( $projectId, $model, $timestamp, $dateObject ) {
        $this->beginTran();
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        if ( !$project ) {
            $this->rollback();
            throw new ApplicationException( "Project: with Id {$projectId} not found", 404 );
        }

        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        if ( $configuration ) {
            $closed = ConfigurationRepository::getInstance()->closeActiveConfiguration( $configuration[ 'configId' ], $timestamp );
            if ( $closed == 0 ) {
                $this->rollback();
                throw new ApplicationException( "Configuration for project with id {$projectId} failed to close", 400 );
            }
        }

        $created = ConfigurationRepository::getInstance()->createNewConfiguration( $projectId, $timestamp );
        if ( $created == 0 ) {
            $this->rollback();
            throw new ApplicationException( "Configuration for project with id {$projectId} failed to create", 400 );
        }

        $activeConfig = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        $initialCommitment = ProjectsRepository::getInstance()->getProjectDurations( $projectId, $activeConfig[ 'configId' ] );
        if ( !$initialCommitment[ 'initialDuration' ] ) {
            $this->assignProjectInitialCommitment( $projectId, $model->duration );
        }

        if ( $model->planRenew == 1 ) {
            TestCasesRepository::getInstance()->clearTestCases( $projectId );
        }

        $this->assignUsersToProject( $projectId, $model->activeUsers, $activeConfig[ 'configId' ] );
        $this->assignDaysToProject( $projectId, $model->duration, $model->expectedTCPD, $activeConfig[ 'configId' ], $dateObject );
        $this->allocateProjectTestCases(
            $projectId,
            $model->nonFinalTestCasesCount,
            $model->expectedTCPD,
            $model->actualTCPD,
            $model->algorithm,
            $activeConfig[ 'configId' ]
        );

        $this->commit();
    }

    public function assignProjectInitialCommitment( $projectId, $duration ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::PROJECT_INITIAL_COMMITMENT );

        $stmt->execute( [ $duration, $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        return $stmt->rowCount();
    }

    public function assignUsersToProject( $projectId, $activeUsers, $configId ) {
        foreach ( $activeUsers as $user ) {
            if ( !is_object( $user ) ) {
                continue;
            }

            UserRepository::getInstance()->assignUserToProject( $projectId, $user->id, $user->load, $user->performance, $configId );
        }
    }

    public function assignDaysToProject( $projectId, $duration, $expected, $configId, $dateObject ) {
        /** @var \Datetime $date */
        $date = $dateObject;
        $i = 0;

        while ( $i < $duration ) {
            while ( $this->isWeekend( $date->format( 'Y-m-d' ) ) ) {
                $date = $date->add( new DateInterval( 'P' . 1 . 'D' ) );
            }

            $dateStr = $date->format( 'Y-m-d' );
            $date = $date->add( new DateInterval( 'P' . 1 . 'D' ) );
            $i++;

            DaysRepository::getInstance()->assignDayToProject( $projectId, $i, $dateStr, $expected, $configId );
        }
    }

    public function allocateProjectTestCases( $projectId, $testCasesCount, $expected, $actual, $algorithm, $newConfigId ) {
        $users = ConfigurationRepository::getInstance()->getProjectAssignedUsers( $projectId );
        $days = DaysRepository::getInstance()->getProjectAssignedDays( $projectId, $newConfigId );
        $testCases = TestCasesRepository::getInstance()->getProjectUnallocatedTestCases( $projectId );

        $ratio = $actual / $expected;
        $allocatedCount = 0;

        foreach ( $days as $day => $dayValue ) {
            foreach ( $users as $userK => $userV ) {
                if ( $algorithm == 1 ) {
                    $userTestCasesCount = $userV [ 'performanceIndicator' ];
                } else {
                    $userTestCasesCount = round( $userV [ 'performanceIndicator' ] * $ratio );
                }

                for ( $i = 0; $i < $userTestCasesCount; $i++ ) {
                    if ( $allocatedCount == $testCasesCount ) {
                        return;
                    }

                    TestCasesRepository::getInstance()->allocateTestCase(
                        $testCases[ $allocatedCount ][ 'testCaseId' ],
                        $userV[ 'userId' ],
                        $dayValue[ 'dayId' ]
                    );

                    $allocatedCount++;
                }
            }
        }
    }

    private function isWeekend( $date ) {
        return ( date( 'N', strtotime( $date ) ) == 5 || date( 'N', strtotime( $date ) ) == 6 );
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}