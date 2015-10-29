<?php

namespace Medieval\Controllers;

use Medieval\ViewModels\ProfileViewModel;
use Medieval\Repositories\UserRepository;
use Medieval\Framework\BaseController;

class ProfilesController extends BaseController {

    /**
     * @authorize
     * @customRoute('profiles/me')
     */
    public function myProfile() {
        $userInfo = UserRepository::getInstance()->getLoggedUserInfo( $this->getUserId() );

        $viewModel = new ProfileViewModel();
        $viewModel->setUsername( $userInfo[ 'username' ] );

        $this->_view->appendToLayout( 'layouts.profile', 'profile.myProfile', $viewModel );
        $this->_view->render( 'layouts.profile' );
    }
}