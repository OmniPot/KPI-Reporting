<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\LoginBindingModel;
use KPIReporting\Repositories\UserRepository;
use KPIReporting\Framework\BaseController;

class UsersController extends BaseController {

    /**
     * @method POST
     * @customRoute('user/login')
     * @param LoginBindingModel $model
     * @return string
     */
    public function login( LoginBindingModel $model ) {
        $username = $model->username;
        $password = $model->password;

        return $this->initLogin( $username, $password );
    }

    /**
     * @authorize
     * @method POST
     * @customRoute('user/logout')
     */
    public function logout() {
        session_destroy();
        die( 'Successfully logged out' );
    }

    private function initLogin( $username, $password ) {
        $userInfo = UserRepository::getInstance()->login( $username, $password );

        $_SESSION = [ ];
        $_SESSION[ 'id' ] = $userInfo[ 'id' ];
        $_SESSION[ 'role' ] = $userInfo[ 'role' ];

        return $userInfo;
    }

    /**
     * @authorize
     * @customRoute('users/all')
     * @return mixed
     * @internal param $username
     */
    public function getAllUsers() {
        $userInfo = UserRepository::getInstance()->getAllUsers();

        return $userInfo;
    }
}