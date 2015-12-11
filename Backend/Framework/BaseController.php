<?php

namespace KPIReporting\Framework;

use DateTimeZone;
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

    protected function getCurrentDateObject() {
        return new \DateTime( 'now', new DateTimeZone( "Asia/Qatar" ) );
    }

    protected function getCurrentDate() {
        $date = new \DateTime( 'now', new DateTimeZone( "Asia/Qatar" ) );

        return $date->format( 'Y-m-d' );
    }

    protected function getCurrentDateTime() {
        $dateTime = new \DateTime( 'now', new DateTimeZone( "Asia/Qatar" ) );

        return $dateTime->format( 'Y-m-d h-i-s' );
    }
}