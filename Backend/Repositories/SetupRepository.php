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
        $stmt = $this->getDatabaseInstance()->prepare( InsertQueries::REPLICATE_PROJECT );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->rowCount();
    }

    public function saveProjectSetup( $projectId, $model, $time, $date ) {
        $existingConfig = ConfigurationRepository::getInstance()->getExistingProjectConfiguration( $projectId );
        $activeConfig = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        if ( !$activeConfig ) {
            $this->beginTran();
            if ( !$existingConfig ) {
                $this->assignProjectInitialCommitment( $projectId, $model->duration );
            }
            $this->process( $projectId, $model->duration, $model->activeUsers, $model->algorithm, $time, $date );
        } else if ( $activeConfig ) {
            $this->beginTran();
            $this->allocateTestCases( $projectId, $model->algorithm, $activeConfig[ 'configId' ], $date );
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

    public function allocateTestCases( $projectId, $algorithm, $newConfigId, $date ) {
        $users = ProjectsRepository::getInstance()->getProjectAssignedUsers( $projectId, $newConfigId );
        $days = DaysRepository::getInstance()->getProjectAssignedDays( $projectId );

        $unallocated = TestCasesRepository::getInstance()->getProjectUnallocatedTestCases( $projectId );
        $expired = TestCasesRepository::getInstance()->getProjectExpiredNonFinalTestCases( $projectId, $date );

        $testCasesToAllocate = array_merge( $unallocated, $expired );

        $actualTCPD = count( $testCasesToAllocate ) / count( $days );
        $expectedTCPD = $this->calcExpectedTCPD( $users );

        $ratio = $actualTCPD / $expectedTCPD;
        if ( $actualTCPD < $expectedTCPD && $algorithm == 1 ) {
            $ratio = 1;
        }

        $allocatedCount = 0;

        foreach ( $days as $day => $dayValue ) {
            foreach ( $users as $userK => $userV ) {
                $userTestCasesCount = round( $userV [ 'performanceIndicator' ] * $ratio );

                for ( $i = 0; $i < $userTestCasesCount; $i++ ) {
                    // Check if all testCases are allocated
                    $noMoreTestCases = $allocatedCount == count( $testCasesToAllocate );

                    // Make sure to transfer test cases only to the current or next days
                    $dayDate = new \DateTime( $dayValue[ 'dayDate' ] );
                    $currentDate = new \DateTime( $date );

                    if ( $noMoreTestCases || $dayDate < $currentDate ) {
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
    }

    public function clearSetup( $projectId, $config, $time, $reason ) {
        $this->beginTran();

        DaysRepository::getInstance()->insertPlanChange(
            $time,
            isset( $reason->duration ) ? $reason->duration : null,
            null,
            isset( $reason->explanation ) ? $reason->explanation : null,
            $projectId,
            $reason->id,
            $config[ 'configId' ]
        );
        TestCasesRepository::getInstance()->clearRemainingTestCasesOnPlanReset( $projectId );
        DaysRepository::getInstance()->clearRemainingDaysOnPlanReset( $projectId );
        ConfigurationRepository::getInstance()->closeActiveConfiguration( $config[ 'configId' ], $time );

        $this->commit();
    }

    private function process( $projectId, $duration, $activeUsers, $algorithm, $time, $date ) {
        $newConfig = ConfigurationRepository::getInstance()->createNewConfiguration( $projectId, $time );
        $expectedTCPD = $this->calcExpectedTCPD( $activeUsers );

        $this->assignUsersToProject( $projectId, $activeUsers, $newConfig[ 'configId' ] );
        $this->assignDaysToProject( $projectId, $duration, $expectedTCPD, $newConfig[ 'configId' ] );
        $this->allocateTestCases( $projectId, $algorithm, $newConfig[ 'configId' ], $date );

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