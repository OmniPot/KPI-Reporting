<?php

namespace Medieval\ViewModels;

class ProjectViewModel extends BaseViewModel {

    private $project;

    public function getProject() {
        return $this->project;
    }

    public function setProject( $project ) {
        $this->project = $project;
    }
}