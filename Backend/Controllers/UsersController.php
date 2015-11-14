<?php

namespace KPIReporting\Controllers;

use KPIReporting\BindingModels\LoginBindingModel;
use KPIReporting\BindingModels\RegisterBindingModel;
use KPIReporting\Exceptions\ApplicationException;
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
     * @method POST
     * @customRoute('user/register')
     * @param RegisterBindingModel $model
     * @return array|bool|string
     * @throws ApplicationException
     */
    public function register( RegisterBindingModel $model ) {
        $username = $model->username;
        $password = $model->password;
        $confirm = $model->confirm;
        $email = $model->email ? $model->email : null;

        if ( $password !== $confirm ) {
            throw new ApplicationException ( 'Password confirmation does not match', 400 );
        }

        UserRepository::getInstance()->register( $username, $password, $email );

        return $this->initLogin( $username, $password );
    }

    /**
     * @method POST
     * @customRoute('user/logout')
     */
    public function logout() {
        session_destroy();
        die( 'Successfully logged out' );
    }

    /**
     * @authorize
     * @customRoute('user/me')
     */
    public function myProfile() {
        $userInfo = UserRepository::getInstance()->getLoggedUserInfo();

        return $userInfo;
    }

    /**
     * @authorize
     * @customRoute('user/string')
     * @param $username
     * @return mixed
     */
    public function getUserInfo( $username ) {
        $userInfo = UserRepository::getInstance()->getUserInfo( $username );

        return $userInfo;
    }

    private function initLogin( $username, $password ) {
        $userInfo = UserRepository::getInstance()->login( $username, $password );

        $_SESSION = [ ];
        $_SESSION[ 'id' ] = $userInfo[ 'id' ];
        $_SESSION[ 'role' ] = $userInfo[ 'role' ];

        return $userInfo;
    }
}