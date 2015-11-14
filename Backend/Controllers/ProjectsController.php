<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\ProjectBindingModel;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\BaseController;
use KPIReporting\Repositories\ProjectsRepository;

class ProjectsController extends BaseController {

    /**
     * @customRoute('projects/all')
     * @return string
     */
    public function getAll() {
        $projects = ProjectsRepository::getInstance()->getAllProjects();

        return $projects;
    }

    /**
     * @customRoute('projects/int')
     * @param int $projectId
     * @return string
     * @throws ApplicationException
     */
    public function getById( $projectId ) {
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        if ( !$project ) {
            throw new ApplicationException( "No project with id {$projectId} found", 404 );
        }

        return $project;
    }

    /**
     * @customRoute('projects/int/testCases')
     * @param int $projectId
     * @return string
     * @throws ApplicationException
     */
    public function getProjectTestCases( $projectId ) {
        $projectTestCases = ProjectsRepository::getInstance()->getProjectTestCases( $projectId );

        if ( !$projectTestCases ) {
            throw new ApplicationException( "Error while fetching test cases for project with id: {$projectId}", 404 );
        }

        return $projectTestCases;
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('projects/create')
     * @param ProjectBindingModel $projectModel
     * @return int
     */
    public function create( ProjectBindingModel $projectModel ) {
        $name = $projectModel->name;
        $duration = $projectModel->duration;
        $description = $projectModel->description ? $projectModel->description : null;
        $startDate = $projectModel->start_date ? new \DateTime( $projectModel->start_date ) : null;
        $endDate = $projectModel->end_date ? new \DateTime( $projectModel->end_date ) : null;

        $projectCreationResult = ProjectsRepository::getInstance()->createNewProject(
            $name,
            $duration,
            $description,
            $startDate,
            $endDate
        );

        return $projectCreationResult;
    }
}