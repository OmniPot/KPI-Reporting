<?php

namespace KPIReporting\Repositories;

use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseRepository;

class ProjectsRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getAllProjects() {
        $allProjectsQuery =
            "SELECT
                p.id,
                p.name,
                p.description,
                p.duration,
                p.start_date,
                p.end_date
            FROM
                projects p";

        $result = $this->getDatabaseInstance()->prepare( $allProjectsQuery );
        $result->execute();

        $projects = $result->fetchAll();

        return $projects;
    }

    public function getProjectById( $projectId ) {
        $projectQuery =
            "SELECT
                p.id,
                p.name,
                p.description,
                p.duration,
                p.start_date,
                p.end_date
            FROM
                Projects p
            WHERE p.id = ?";

        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
        $result->execute( [ $projectId ] );

        $project = $result->fetch();

        return $project;
    }

    public function getProjectByName( $projectName ) {
        $projectQuery =
            "SELECT
                p.id,
                p.name,
                p.description,
                p.duration,
                p.start_date,
                p.end_date
            FROM
                Projects p
            WHERE p.name = ?";

        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
        $result->execute( [ $projectName ] );

        return $result->fetch();
    }

    public function createNewProject( $name, $duration, $description, $startDate, $endDate ) {
        if ( $this->getProjectByName( $name ) ) {
            throw new ApplicationException( "Project: \"{$name}\" already exists" );
        }

        $createQuery =
            "INSERT INTO projects(
                name,
                duration,
                description,
                start_date,
                end_date
            ) VALUES(?, ?, ?, ?, ?)";

        $result = $this->getDatabaseInstance()->prepare( $createQuery );
        $result->execute(
            [
                $name,
                $duration,
                $description,
                $startDate ? $startDate->format( 'Y-m-d H:i:s' ) : null,
                $endDate ? $endDate->format( 'Y-m-d H:i:s' ) : null
            ]
        );

        return $this->getProjectByName( $name );
    }

    public function getProjectTestCases( $projectId ) {
        $projectQuery =
            "SELECT
                tc.title,
                d.number AS 'day',
                s.name AS 'status'
            FROM test_cases tc
            JOIN statuses s ON s.id = tc.status_id
            JOIN days d ON d.id = tc.day_id
            WHERE tc.project_id = ?";

        $result = $this->getDatabaseInstance()->prepare( $projectQuery );
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