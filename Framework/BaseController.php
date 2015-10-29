<?php

namespace Medieval\Framework;

use Medieval\Config\RoutingConfig;
use Medieval\Framework\Config\FrameworkConfig;
use Medieval\Framework\Database\Database;
use Medieval\Framework\Routers\RequestUriResult;
use Medieval\Repositories\UserRepository;

class BaseController {

    /** @var  View $_view */
    protected $_view;

    protected $_areaName;
    protected $_controllerName;
    protected $_actionName;
    protected $_requestParams;

    protected $alreadyAuthorizedLocation = RoutingConfig::AUTHORIZED_REDIRECT;
    protected $unauthorizedLocation = RoutingConfig::UNAUTHORIZED_REDIRECT;

    public function __construct( RequestUriResult $requestParseResult, $view ) {
        $this->_areaName = $requestParseResult->getAreaName();
        $this->_controllerName = $requestParseResult->getControllerName();
        $this->_actionName = $requestParseResult->getActionName();

        $this->_requestParams = $requestParseResult->getRequestParams();
        $this->_view = $view;
    }

    protected function isAuthenticated() {
        return isset( $_SESSION[ 'id' ] );
    }

    protected function getUserId() {
        return isset( $_SESSION[ 'id' ] ) ? $_SESSION[ 'id' ] : null;
    }

    protected function getLoggedUserInfo() {
        $userRepo = UserRepository::getInstance();
        $user = $userRepo->getLoggedUserInfo();
    }

    protected function redirect( $location ) {
        if ( !$location ) {
            throw new \Exception( 'Invalid location' );
        }

        $fullUri = $_SERVER[ 'REQUEST_URI' ];
        $customUri = $_GET[ 'uri' ];

        $newUri = str_replace( $customUri, $location, $fullUri );
        $newUri = str_replace( FrameworkConfig::VENDOR_NAMESPACE, '', $newUri );

        header( 'Location: ' . $newUri );
        exit;
    }
}