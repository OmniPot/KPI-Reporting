<?php

namespace KPIReporting\Controllers;

use DateInterval;
use KPIReporting\BindingModels\SetupBindingModel;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ConfigurationRepository;
use KPIReporting\Repositories\DaysRepository;
use KPIReporting\Repositories\ProjectsRepository;
use KPIReporting\Repositories\SetupRepository;
use KPIReporting\Repositories\TestCasesRepository;

class SetupController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @customRoute('projects/int/setup')
     * @param int $projectId
     * @return string
     * @throws ApplicationException
     */
    public function getProjectSetupPage( $projectId ) {
        $project = SetupRepository::getInstance()->checkIfProjectSourceExists( $projectId );
        if ( !$project ) {
            throw new ApplicationException( "Project: with Id {$projectId} not found in ooredoo_pipeline", 404 );
        }

        $replicated = SetupRepository::getInstance()->checkIfProjectIsReplicated( $projectId );
        if ( !$replicated ) {
            SetupRepository::getInstance()->replicateProject( $projectId );
        }

        $details = [ ];
        $details[ 'activeUsers' ] = ConfigurationRepository::getInstance()->getProjectAssignedUsers( $projectId );
        $details[ 'initialCommitment' ] = ProjectsRepository::getInstance()->getProjectInitialCommitment( $projectId );

        return $details;
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/int/setup/save')
     * @param $projectId
     * @param SetupBindingModel $model
     * @return string
     * @throws ApplicationException
     */
    public function saveProjectSetup( $projectId, SetupBindingModel $model ) {
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        if ( !$project ) {
            throw new ApplicationException( "Project: with Id {$projectId} not found", 404 );
        }

        $configuration = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );
        if ( $configuration ) {
            ConfigurationRepository::getInstance()->closeActiveConfiguration( $configuration[ 'configId' ], $this->getCurrentDateTime() );
        }

        $created = ConfigurationRepository::getInstance()->createNewConfiguration( $projectId, $this->getCurrentDateTime() );
        if ( !$created ) {
            throw new ApplicationException( "Configuration for project with id {$projectId} bot created", 404 );
        }

        $newConfig = ConfigurationRepository::getInstance()->getActiveProjectConfiguration( $projectId );

        $initialCommitment = ProjectsRepository::getInstance()->getProjectInitialCommitment( $projectId );
        if ( !$initialCommitment[ 'initialCommitment' ] ) {
            SetupRepository::getInstance()->assignProjectInitialCommitment( $projectId, $model->duration );
        }

        $this->assignUsersToProject( $projectId, $model->activeUsers, $newConfig[ 'configId' ] );
        $this->assignDaysToProject(
            $projectId,
            $model->duration,
            $model->testCasesCount,
            $model->testCasesPerDay,
            $model->algorithm,
            $newConfig[ 'configId' ]
        );
        $this->allocateTestCases( $projectId, $newConfig[ 'configId' ] );

        return $model;
    }

    private function assignUsersToProject( $projectId, $activeUsers, $configId ) {
        foreach ( $activeUsers as $user ) {
            if ( !is_object( $user ) ) {
                continue;
            }

            $assignResult = SetupRepository::getInstance()->assignUserToProject(
                $projectId,
                $user->id,
                $user->load,
                $user->performance,
                $configId
            );

            if ( !$assignResult ) {
                throw new ApplicationException( "Assigning user {$user->id} to project {$projectId} failed.", 400 );
            }
        }
    }

    private function assignDaysToProject( $projectId, $duration, $testCasesCount, $testCasesPerDay, $algorithm, $configId ) {
        $remainingTestCases = $testCasesCount;
        $date = $this->getCurrentDateObject();
        $i = 0;

        while ( $i < $duration ) {
            while ( $this->isWeekend( $date->format( 'Y-m-d' ) ) ) {
                $date = $date->add( new DateInterval( 'P' . 1 . 'D' ) );
            }

            $dateStr = $date->format( 'Y-m-d' );
            $date = $date->add( new DateInterval( 'P' . 1 . 'D' ) );
            $i++;

            if ( $algorithm == 1 ) {
                if ( $remainingTestCases >= $testCasesPerDay ) {
                    $tcToInsert = $testCasesPerDay;
                } else if ( $remainingTestCases < $testCasesPerDay && $remainingTestCases >= 0 ) {
                    $tcToInsert = $remainingTestCases;
                } else {
                    $tcToInsert = 0;
                }

                $insertResult = SetupRepository::getInstance()->assignDayToProject( $projectId, $i, $dateStr, $tcToInsert, $configId );
                $remainingTestCases = $remainingTestCases - $testCasesPerDay;
            } else if ( $algorithm == 2 ) {
                $insertResult = SetupRepository::getInstance()->assignDayToProject( $projectId, $i, $dateStr, $testCasesPerDay, $configId );
            }

            if ( !$insertResult ) {
                throw new ApplicationException( "Algo 2 - Day " . ( $i + 1 ) . " not assigned to project" );
            }
        }
    }

    private function allocateTestCases( $projectId, $configId ) {
        $users = ConfigurationRepository::getInstance()->getProjectAssignedUsers( $projectId );
        $days = DaysRepository::getInstance()->getProjectAssignedDays( $projectId, $configId );
        $testCases = TestCasesRepository::getInstance()->getProjectUnallocatedTestCases( $projectId );

        $overallTestCasesCount = count( $testCases );
        $overallIndex = 0;

        foreach ( $days as $day => $dayValue ) {
            foreach ( $users as $userKey => $userValue ) {
                $users[ $userKey ][ 'dailyRatio' ] = $userValue[ 'performanceIndicator' ] / $dayValue[ 'expectedTestCases' ];
            }

            foreach ( $users as $userK => $userV ) {
                $userTestCasesCount = $dayValue[ 'expectedTestCases' ] * $userV [ 'dailyRatio' ];
                $userTestCasesIndex = 0;

                while ( $userTestCasesIndex < $userTestCasesCount ) {
                    if ( $overallIndex >= $overallTestCasesCount ) {
                        return;
                    }

                    $assignResult = TestCasesRepository::getInstance()->allocateTestCase(
                        $testCases[ $overallIndex ][ 'testCaseId' ],
                        $userV[ 'userId' ],
                        $dayValue[ 'dayId' ],
                        $configId
                    );

                    if ( !$assignResult ) {
                        throw new ApplicationException( "Allocation of test case {$overallIndex} failed." );
                    }

                    $userTestCasesIndex++;
                    $overallIndex++;
                }
            }
        }
    }

    private function isWeekend( $date ) {
        return ( date( 'N', strtotime( $date ) ) == 5 || date( 'N', strtotime( $date ) ) == 6 );
    }
}