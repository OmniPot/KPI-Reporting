<?php

namespace Medieval\Repositories;

use Medieval\Framework\BaseRepository;

class ProjectsRepository extends BaseRepository {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct();
    }

    public function getAllProjects() {

        $allProjectsQuery = "
            SELECT
                p.id,
                p.name,
                p.description,
                p.duration,
                p.start_date,
                p.end_date
            FROM
                Projects p";

        $result = $this->getDatabaseInstance()->prepare( $allProjectsQuery );
        $result->execute();

        $projects = $result->fetchAll();

        return $projects;
    }

    public function getProjectById( $projectId ) {

        $projectQuery = "
            SELECT
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

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}