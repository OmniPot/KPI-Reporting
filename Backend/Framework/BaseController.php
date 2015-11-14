<?php

namespace KPIReporting\Framework;

use KPIReporting\Config\RoutingConfig;
use KPIReporting\Framework\Config\FrameworkConfig;
use KPIReporting\Framework\Routers\RequestUriResult;
use KPIReporting\Repositories\UserRepository;

class BaseController {

    protected $_areaName;
    protected $_controllerName;
    protected $_actionName;
    protected $_requestParams;

    protected $alreadyAuthorizedLocation = RoutingConfig::AUTHORIZED_REDIRECT;
    protected $unauthorizedLocation = RoutingConfig::UNAUTHORIZED_REDIRECT;

    public function __construct( RequestUriResult $requestParseResult ) {
        $this->_areaName = $requestParseResult->getAreaName();
        $this->_controllerName = $requestParseResult->getControllerName();
        $this->_actionName = $requestParseResult->getActionName();

        $this->_requestParams = $requestParseResult->getRequestParams();
    }

    protected function isAuthenticated() {
        return isset( $_SESSION[ 'id' ] );
    }

    protected function getUserId() {
        return isset( $_SESSION[ 'id' ] ) ? $_SESSION[ 'id' ] : null;
    }

    protected function getLoggedUserInfo() {
        $userInfo = UserRepository::getInstance()->getLoggedUserInfo();

        return $userInfo;
    }

    protected function redirect( $location ) {
        if ( !$location ) {
            throw new \Exception( 'Invalid location', 404 );
        }

        $fullUri = $_SERVER[ 'REQUEST_URI' ];
        $customUri = $_GET[ 'uri' ];

        $newUri = str_replace( $customUri, $location, $fullUri );
        $newUri = str_replace( FrameworkConfig::VENDOR_NAMESPACE, '', $newUri );

        header( 'Location: ' . $newUri );
        exit;
    }
}