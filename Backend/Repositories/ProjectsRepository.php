<?php

namespace KPIReporting\Repositories;

use KPIReporting\Config\Queries;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;

class ProjectsRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getAllProjects() {
        $allProjectsQuery = Queries::ALL_PROJECTS;
        $result = $this->getDatabaseInstance()->prepare( $allProjectsQuery );
        $result->execute();

        $projects = $result->fetchAll();

        return $projects;
    }

    public function getProjectById( $projectId ) {
        $projectQuery = Queries::PROJECT_BY_ID;
        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
        $result->execute( [ $projectId ] );

        if ( !$result->rowCount() ) {
            throw new ApplicationException( "Project: with ID {$projectId} not found", 404 );
        }

        $project = $result->fetch();

        $projectTestCases = Queries::PROJECT_TEST_CASES;
        $result = $this->getDatabaseInstance()->prepare( $projectTestCases );
        $result->execute( [ $projectId ] );

        $project[ 'testCases' ] = $result->fetchAll();

        return $project;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}