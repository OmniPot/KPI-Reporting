<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\CountQueries;
use KPIReporting\Queries\SelectQueries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;
use PDO;

class ProjectsRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getProjectById( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_BY_ID );
        $result = $stmt->execute( [ $projectId ] );

        if ( !$result ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $project = $stmt->fetch();

        return $project;
    }

    public function getProjectTestCasesForSync( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_SYNC_TEST_CASES );

        $stmt->execute( [ $projectId ] );
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getProjectTestCasesForAllocationMap( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_ALLOCATION_MAP_TEST_CASES );
        $stmt->bindParam( 1, $projectId, PDO::PARAM_INT );

        $stmt->execute();
        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetchAll();
    }

    public function getProjectAssignedUsers( $projectId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_ASSIGNED_USERS );

        $stmt->execute( [ $projectId, $configId ] );
        if ( !$stmt ) {
            $this->rollback();
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 400 );
        }

        return $stmt->fetchAll();
    }

    public function getProjectCurrentDuration( $projectId, $configId ) {
        $stmt = $this->getDatabaseInstance()->prepare( CountQueries::GET_PROJECT_CURRENT_DURATION );
        $stmt->execute( [ $projectId, $configId ] );

        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $duration = $stmt->fetch();

        return $duration[ 'currentDuration' ];
    }

    public function getProjectInitialCommitment( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_PROJECT_INITIAL_COMMITMENT );
        $stmt->execute( [ $projectId ] );

        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        $duration = $stmt->fetch();

        return $duration[ 'initialCommitment' ];
    }

    public function getTestLinkProject( $projectId ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_TESTLINK_PROJECT );
        $stmt->bindParam( 1, '%[id=' . $projectId . ']%', \PDO::PARAM_STR );
        $stmt->execute();

        if ( !$stmt ) {
            throw new ApplicationException( implode( "\n", $stmt->getErrorInfo() ), 500 );
        }

        return $stmt->fetch();
    }

    public function getChildNodes( $node, $result ) {
        $stmt = $this->getDatabaseInstance()->prepare( SelectQueries::GET_CHILD_NODES );
        $stmt->bindParam( 1, $node[ 'nodeId' ], \PDO::PARAM_INT );
        $stmt->execute();

        $resultNodes = $stmt->fetchAll();

        foreach ( $resultNodes as $nK => $nV ) {
            if ( $nV[ 'nodeTypeId' ] == 2 ) {
                $result = $this->getChildNodes( $nV, $result );
            } else {
                $result[] = $nV;
            }
        }

        return $result;
    }

    public function syncProjectTestCases( $externalProjectId ) {
        $testLinkProject = $this->getTestLinkProject( $externalProjectId );
        $testLinkTestCases = $this->getChildNodes( $testLinkProject, [ ] );
        $reportingTestCases = $this->getProjectTestCasesForSync( $externalProjectId );

        $this->insertMissing( $externalProjectId, $testLinkTestCases, $reportingTestCases );
        $this->markDeleted( $testLinkTestCases, $reportingTestCases );
        $this->updateExisting( $testLinkTestCases, $reportingTestCases );

        return [ 'msg' => 'Sync successful!' ];
    }

    private function insertMissing( $externalProjectId, $testLinkTestCases, $reportingTestCases ) {
        foreach ( $testLinkTestCases as $testLinkK => $testLinkV ) {
            $exists = false;
            foreach ( $reportingTestCases as $reportingK => $reportingV ) {
                if ( $testLinkV[ 'nodeId' ] == $reportingV[ 'externalId' ] ) {
                    $exists = true;
                }
            }

            if ( !$exists ) {
                TestCasesRepository::getInstance()->insertTestCase( $testLinkV[ 'nodeName' ], $testLinkV[ 'nodeId' ], $externalProjectId );
            }
        }
    }

    private function markDeleted( $testLinkTestCases, $reportingTestCases ) {
        foreach ( $reportingTestCases as $reportingK => $reportingV ) {
            $forDeletion = true;
            foreach ( $testLinkTestCases as $testLinkK => $testLinkV ) {
                if ( $testLinkV[ 'nodeId' ] == $reportingV[ 'externalId' ] ) {
                    $forDeletion = false;
                }
            }

            if ( $forDeletion ) {
                TestCasesRepository::getInstance()->markDeleted( $reportingV[ 'externalId' ] );
            }
        }
    }

    private function updateExisting( $testLinkTestCases, $reportingTestCases ) {
        foreach ( $reportingTestCases as $reportingK => $reportingV ) {
            foreach ( $testLinkTestCases as $testLinkK => $testLinkV ) {
                if ( $testLinkV[ 'nodeId' ] == $reportingV[ 'externalId' ] &&
                    $testLinkV[ 'nodeName' ] != $reportingV[ 'testCaseTitle' ]
                ) {
                    TestCasesRepository::getInstance()->updateExistingTestCase(
                        $reportingV[ 'externalId' ],
                        $testLinkV[ 'nodeName' ]
                    );
                }
            }
        }
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}