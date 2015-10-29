<?php

namespace Medieval\Controllers;

use Medieval\Repositories\UserRepository;
use Medieval\Framework\BaseController;
use Medieval\Repositories\ProjectsRepository;
use Medieval\ViewModels\ProjectsViewModel;
use Medieval\ViewModels\ProjectViewModel;

class ProjectsController extends BaseController {

    /**
     * @authorized
     * @customRoute('projects/all')
     */
    public function getAll() {
        $user = UserRepository::getInstance()->getLoggedUserInfo();
        $projects = ProjectsRepository::getInstance()->getAllProjects();

        $projectsViewModel = new ProjectsViewModel();
        $projectsViewModel->setUsername( $user[ 'username' ] );
        $projectsViewModel->setProjects( $projects );

        $this->_view->appendToLayout( 'layouts.projects', 'projects.all', $projectsViewModel );
        $this->_view->render( 'layouts.projects' );
    }

    /**
     * @authorized
     * @customRoute('projects/int');
     * @param int $projectId
     */
    public function getById( $projectId ) {
        $user = UserRepository::getInstance()->getUserInfo( $this->getUserId() );
        $project = ProjectsRepository::getInstance()->getProjectById( $projectId );

        $projectViewModel = new ProjectViewModel();
        $projectViewModel->setUsername( $user[ 'username' ] );

        $layout = 'layouts.projectById';
        $template = 'projects.single';

        if ( !$project ) {
            $layout = 'layouts.error';
            $template = 'error';

            $projectViewModel->setError( 'Page not found, sorry :(' );
        }

        $projectViewModel->setProject( $project );
        $this->_view->appendToLayout( $layout, $template, $projectViewModel );
        $this->_view->render( $layout );
    }
}