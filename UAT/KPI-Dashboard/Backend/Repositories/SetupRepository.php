<?php

namespace KPIReporting\Repositories;

use DateInterval;
use KPIReporting\Config\AppConfig;
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
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::REPLICATE_PROJECT );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->rowCount();
    }

    public function saveProjectSetup( $projectId, $model, $date ) {
        $existingConfig = ConfigurationRepository::getInstance()->getExistingProjectConfiguration( $projectId );
        $activeConfig = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        if ( !$activeConfig ) {
            $this->beginTran();
            if ( !$existingConfig ) {
                $this->assignProjectInitialCommitment( $projectId, $model->duration );
            }
            $this->process( $projectId, $model->duration, $model->activeUsers, $model->algorithm, $date );
        } else if ( $activeConfig ) {
            $this->beginTran();
            $this->allocateTestCases( $projectId, $model->algorithm, $activeConfig[ 'configId' ], $date, true );
            $this->commit();
        }
    }

    public function assignProjectInitialCommitment( $projectId, $duration ) {
        $stmt = $this->getDatabaseInstance()->prepare( UpdateQueries::PROJECT_INITIAL_COMMITMENT );

        $stmt->execute( [ $duration, $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->rowCount();
    }

    public function assignUsersToProject( $projectId, $activeUsers, $configId ) {
        foreach ( $activeUsers as $user ) {
            if ( is_object( $user ) ) {
                UserRepository::getInstance()->assignUserToProject(
                    $projectId,
                    $user->id,
                    $user->loadIndicator,
                    $user->performanceIndicator,
                    $configId
                );
            }

            if ( is_array( $user ) ) {
                UserRepository::getInstance()->assignUserToProject(
                    $projectId,
                    $user[ 'userId' ],
                    $user[ 'loadIndicator' ],
                    $user[ 'performanceIndicator' ],
                    $configId
                );
            }
        }
    }

    public function assignDaysToProject( $projectId, $duration, $tcpd, $configId, $extensionKey = null ) {
        $lastProjectDay = DaysRepository::getInstance()->getLastProjectDay( $projectId );

        /** @var \Datetime $date */
        $date = new \DateTime( $lastProjectDay[ 'startDayDate' ] );

        $index = $lastProjectDay[ 'startDayIndex' ];

        for ( $i = 0; $i < $duration; $i++ ) {
            while ( $this->isWeekend( $date->format( 'Y-m-d' ) ) ) {
                $date = $date->add( new DateInterval( 'P' . 1 . 'D' ) );
            }

            $dateStr = $date->format( 'Y-m-d' );
            $date = $date->add( new DateInterval( 'P' . 1 . 'D' ) );
            $index++;

            DaysRepository::getInstance()->assignDayToProject( $projectId, $index, $dateStr, $tcpd, $extensionKey, $configId );
        }
    }

    public function allocateTestCases( $projectId, $algorithm, $newConfigId, $date, $configExists ) {
        $users = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $newConfigId );
        $assignedDays = DaysRepository::getInstance()->getProjectAssignedDays( $projectId );
        $remainingDays = DaysRepository::getInstance()->getProjectRemainingDays( $projectId );
        $lastConfigResetDate = DaysRepository::getInstance()->getLastConfigReset( $projectId );
        $unallocated = TestCasesRepository::getInstance()->getProjectUnallocatedTestCases( $projectId );
        $expired = TestCasesRepository::getInstance()->getProjectExpiredNonFinalTestCases( $projectId );

        $testCasesToAllocate = array_merge( $unallocated, $expired );
        $actualTCPD = count( $testCasesToAllocate ) / count( $remainingDays );
        $expectedTCPD = $this->calcExpectedTCPD( $users );

        $ratio = $actualTCPD / $expectedTCPD;
        if ( $actualTCPD < $expectedTCPD && $algorithm == 1 ) {
            $ratio = 1;
        }

        $allocatedCount = 0;

        foreach ( $assignedDays as $day => $dayValue ) {
            // Make sure to transfer test cases only to the current or next days
            $expiredDay = new \DateTime( $dayValue[ 'dayDate' ] ) < new \DateTime( $date );
            if ( $expiredDay || $dayValue[ 'dayDate' ] == $lastConfigResetDate ) {
                continue;
            }

            $tolerance = 0;
            if ( $configExists ) {
                $tolerance = round( $dayValue[ 'expected' ] * AppConfig::PERCENTAGE_TOLERANCE_TEST_CASES_PER_DAY / 100 );
            }

            foreach ( $users as $userK => $userV ) {
                $userTestCasesCount = round( $userV [ 'performanceIndicator' ] * $ratio );

                for ( $i = 0; $i < $userTestCasesCount; $i++ ) {
                    // Check if all testCases are allocated
                    $noMoreTestCases = $allocatedCount == count( $testCasesToAllocate );

                    $isDayFull = false;
                    if ( $ratio == 1 && $configExists ) {
                        $isDayFull = $dayValue[ 'allocated' ] >= ( $dayValue[ 'expected' ] + $tolerance );
                    }

                    if ( $noMoreTestCases || $isDayFull ) {
                        break;
                    }

                    // Keep the user of the test case if it exists else set the current user's
                    $userId = $userV[ 'userId' ];
                    if ( isset( $testCasesToAllocate[ $allocatedCount ][ 'userId' ] ) ) {
                        $userId = $testCasesToAllocate[ $allocatedCount ][ 'userId' ];
                    }

                    // Keep the status of the testCase if it exists else default
                    $statusId = 1;
                    if ( isset( $testCasesToAllocate[ $allocatedCount ][ 'statusId' ] ) ) {
                        $statusId = $testCasesToAllocate[ $allocatedCount ][ 'statusId' ];
                    }

                    TestCasesRepository::getInstance()->allocateTestCase(
                        $testCasesToAllocate[ $allocatedCount ][ 'testCaseId' ],
                        $userId,
                        $dayValue[ 'dayId' ],
                        $statusId
                    );

                    $dayValue[ 'allocated' ]++;
                    $allocatedCount++;
                }
            }
        }

        if ( $configExists && $allocatedCount < count( $testCasesToAllocate ) ) {
            $this->rollback();
            throw new ApplicationException( 'Automatic allocation of test cases not possible due to insufficient quota!', 400 );
        }
    }

    public function clearSetup( $projectId, $config, $reason ) {
        $this->beginTran();

        DaysRepository::getInstance()->insertPlanChange( null, null, $reason->explanation, $projectId, $reason->id, $config[ 'configId' ] );
        TestCasesRepository::getInstance()->clearRemainingTestCases( $projectId );
        DaysRepository::getInstance()->clearRemainingDays( $projectId );
        ConfigurationRepository::getInstance()->closeActiveConfiguration( $config[ 'configId' ] );

        $this->commit();
    }

    private function process( $projectId, $duration, $activeUsers, $algorithm, $date ) {
        $newConfig = ConfigurationRepository::getInstance()->createNewConfiguration( $projectId );
        $expectedTCPD = $this->calcExpectedTCPD( $activeUsers );

        $this->assignUsersToProject( $projectId, $activeUsers, $newConfig[ 'configId' ] );
        $this->assignDaysToProject( $projectId, $duration, $expectedTCPD, $newConfig[ 'configId' ] );
        $this->allocateTestCases( $projectId, $algorithm, $newConfig[ 'configId' ], $date, false );

        $this->commit();
    }

    public function calcExpectedTCPD( $users ) {
        $expectedTCPD = 0;
        foreach ( $users as $userK => $userV ) {
            if ( is_object( $userV ) ) {
                $expectedTCPD += $userV->performanceIndicator;
            }

            if ( is_array( $userV ) ) {
                $expectedTCPD += $userV[ 'performanceIndicator' ];
            }
        };

        return $expectedTCPD;
    }

    public function isWeekend( $date ) {
        return ( date( 'N', strtotime( $date ) ) == 5 || date( 'N', strtotime( $date ) ) == 6 );
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}