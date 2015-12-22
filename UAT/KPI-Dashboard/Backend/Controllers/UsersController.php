<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\LoginBindingModel;
use KPIReporting\Repositories\UserRepository;
use KPIReporting\Framework\BaseController;

class UsersController extends BaseController {

    /**
     * @method POST
     * @customRoute('user/login')
     */
    public function login( LoginBindingModel $model ) {
        $username = $model->username;
        $password = $model->password;

        return $this->initLogin( $username, $password );
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('users/load')
     */
    public function getUsersLoad() {
        $usersLoad = UserRepository::getInstance()->getUsersLoad();

        return $usersLoad;
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
     */
    public function getAllUsers() {
        $userInfo = UserRepository::getInstance()->getAllUsers();

        return $userInfo;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('user/int')
     */
    public function getUserById( $id ) {
        $userInfo = UserRepository::getInstance()->getUserById( $id );

        return $userInfo;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('user/int/load')
     */
    public function getUserLoad( $userId ) {
        $userLoad = UserRepository::getInstance()->getUserLoad( $userId );

        return $userLoad;
    }

    /**
     * @authorize
     * @method GET
     * @customRoute('user/int/date/mixed')
     */
    public function expandUserDay( $userId, $dayDate ) {
        $userDay = UserRepository::getInstance()->expandUserDay( $userId, $dayDate );

        return $userDay;
    }
}