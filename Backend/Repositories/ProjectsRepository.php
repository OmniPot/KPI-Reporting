<?php

namespace KPIReporting\Repositories;

use KPIReporting\Queries\SelectQueries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;
use PDO;

class ProjectsRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getAllProjects() {
        $allProjectsQuery = SelectQueries::GET_ALL_PROJECTS;
        $result = $this->getDatabaseInstance()->prepare( $allProjectsQuery );
        $result->execute();

        $projects = $result->fetchAll();

        return $projects;
    }

    public function getProjectById( $projectId, $timestamp ) {
        $projectQuery = SelectQueries::GET_PROJECT_BY_ID;
        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
        $result->execute( [ $projectId ] );

        if ( !$result->rowCount() ) {
            throw new ApplicationException( "Project: with ID {$projectId} not found", 404 );
        }

        $project = $result->fetch();

        $projectTestCases = SelectQueries::GET_PROJECT_TEST_CASES;
        $result = $this->getDatabaseInstance()->prepare( $projectTestCases );
        $result->bindParam( 1, $timestamp, PDO::PARAM_STR );
        $result->bindParam( 2, $projectId, PDO::PARAM_INT );
        $result->execute();

        $project[ 'testCases' ] = $result->fetchAll();

        return $project;
    }

    public function checkIfProjectIsAllocated( $projectId ) {
        $checkQuery = SelectQueries::CHECK_IF_PROJECT_IS_ALLOCATED;
        $result = $this->getDatabaseInstance()->prepare( $checkQuery );
        $result->execute( [ $projectId ] );

        return $result->fetch();
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}