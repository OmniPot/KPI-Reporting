<?php

namespace Medieval\ViewModels;

class BaseViewModel {

    private $error;
    private $success;

    private $username = 'guest';

    public function getUsername() {
        return $this->username;
    }

    public function setUsername( $username ) {
        $this->username = $username;
    }

    public function getError() {
        return $this->error;
    }

    public function setError( $error ) {
        $this->error = $error;
    }

    public function getSuccess() {
        return $this->success;
    }

    public function setSuccess( $success ) {
        $this->success = $success;
    }
}