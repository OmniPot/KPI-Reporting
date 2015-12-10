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

    public function replicateProject( $projectId ) {
        $this->beginTran();
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::REPLICATE_PROJECT );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( $stmt->getErrorInfo() );
        }

        $this->commit();

        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_BY_ID );
        $stmt->execute( [ $projectId ] );

        return $stmt->fetch();
    }

    public function saveProjectSetup( $projectId, $model, $timestamp, $dateObject, $date ) {
        $this->beginTran();

        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        $initialCommitment = ProjectsRepository::getInstance()->getProjectInitialCommitment( $projectId );

        if ( $initialCommitment == null && !$configuration && $model->planRenew == 0 ) {
            $this->assignProjectInitialCommitment( $projectId, $model->duration );
            ConfigurationRepository::getInstance()->createNewConfiguration( $projectId, $timestamp );
            $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

            $this->assignUsersToProject( $projectId, $model->activeUsers, $configuration[ 'configId' ] );
            $this->assignDaysToProject( $projectId, $model->duration, $model->expectedTCPD, $configuration[ 'configId' ], $dateObject );
            $this->allocateProjectTestCases(
                $projectId,
                $model->algorithm,
                $configuration[ 'configId' ],
                $dateObject,
                $date
            );
        } else if ( $initialCommitment != null && $configuration && $model->planRenew == 1 ) {
            ConfigurationRepository::getInstance()->closeActiveConfiguration( $configuration[ 'configId' ], $timestamp );
            ConfigurationRepository::getInstance()->createNewConfiguration( $projectId, $timestamp );
            $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

            TestCasesRepository::getInstance()->clearTestCases( $projectId );
            $this->assignUsersToProject( $projectId, $model->activeUsers, $configuration[ 'configId' ] );
            $this->assignDaysToProject( $projectId, $model->duration, $model->expectedTCPD, $configuration[ 'configId' ], $dateObject );
            $this->allocateProjectTestCases(
                $projectId,
                $model->algorithm,
                $configuration[ 'configId' ],
                $dateObject,
                $date
            );
        } else if ( $initialCommitment != null && $configuration && $model->planRenew == 0 ) {
            $this->allocateProjectTestCases(
                $projectId,
                $model->algorithm,
                $configuration[ 'configId' ],
                $dateObject,
                $date
            );
        }

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

    public function assignDaysToProject( $projectId, $duration, $tcpd, $configId, $dateObject ) {
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

            DaysRepository::getInstance()->assignDayToProject( $projectId, $i, $dateStr, $tcpd, $configId );
        }
    }

    public function allocateProjectTestCases( $projectId, $algorithm, $newConfigId, $dateObject, $date ) {
        $users = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $newConfigId );
        $days = DaysRepository::getInstance()->getProjectAssignedDays( $projectId, $newConfigId );

        $unallocated = TestCasesRepository::getInstance()->getProjectUnallocatedTestCases( $projectId );
        $expired = TestCasesRepository::getInstance()->getProjectExpiredNonFinalTestCases( $projectId, $dateObject->format( 'Y-m-d' ) );

        $testCasesToAllocate = array_merge( $unallocated, $expired );

        $actualTCPD = count( $testCasesToAllocate ) / count( $days );
        $expectedTCPD = $this->calcExpectedTCPD( $users );
        $ratio = $actualTCPD / $expectedTCPD;
        if ( $actualTCPD < $expectedTCPD && $algorithm == 1 ) {
            $ratio = 1;
        }

        $allocatedCount = 0;

        foreach ( $days as $day => $dayValue ) {
            $dayEmpty = $dayValue[ 'allocatedTestCases' ] == 0;
            $maxForDay = round( $dayValue[ 'expectedTestCases' ] * 1.1 );

            foreach ( $users as $userK => $userV ) {
                $userTestCasesCount = round( $userV [ 'performanceIndicator' ] * $ratio );

                for ( $i = 0; $i < $userTestCasesCount; $i++ ) {
                    // when transferring from expired day to existing day gets filled only to 100% not 100% + tolerance
                    $dayFilled = $maxForDay == $dayValue[ 'allocatedTestCases' ] && !$dayEmpty;

                    // global allocated test cases counter
                    $noMoreTestCases = $allocatedCount == count( $testCasesToAllocate );

                    // make sure to transfer test cases only to the current or next days
                    $dayDate = new \DateTime( $dayValue[ 'dayDate' ] );
                    $currentDate = new \DateTime( $date );

                    if ( $dayFilled || $noMoreTestCases || $dayDate < $currentDate ) {
                        break;
                    }

                    // keep the user of the test case if it exists and if not get a new one
                    $userId = $userV[ 'userId' ];
                    if ( isset( $testCasesToAllocate[ $allocatedCount ][ 'userId' ] ) ) {
                        $userId = $testCasesToAllocate[ $allocatedCount ][ 'userId' ];
                    }

                    TestCasesRepository::getInstance()->allocateTestCase(
                        $testCasesToAllocate[ $allocatedCount ][ 'testCaseId' ],
                        $userId,
                        $dayValue[ 'dayId' ]
                    );

                    $dayValue[ 'allocatedTestCases' ]++;
                    $allocatedCount++;
                }
            }
        }
    }

    private function calcExpectedTCPD( $users ) {
        return $expectedTCPD = round(
            array_reduce(
                $users,
                function ( $a, $user ) {
                    $a += $user[ 'performanceIndicator' ];

                    return $a;
                }
            )
        );
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