<?php

namespace Medieval\ViewModels;

class ProjectsViewModel extends BaseViewModel {

    private $_projects;

    public function getProjects() {
        return $this->_projects;
    }

    public function setProjects( $projects ) {
        $this->_projects = $projects;
    }
}