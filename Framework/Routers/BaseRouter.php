<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\AppConfig;

abstract class BaseRouter {

    private $_areaName;
    private $_controllerName;
    private $_actionName;

    protected $requestParams = [ ];

    protected function __construct() {

        $this->setAreaName( AppConfig::DEFAULT_AREA );
        $this->setControllerName( AppConfig::DEFAULT_CONTROLLER );
        $this->setActionName( AppConfig::DEFAULT_ACTION );
    }

    public function getAreaName() {
        return $this->_areaName;
    }

    protected function setAreaName( $_areaName ) {
        $this->_areaName = $_areaName;
    }

    public function getControllerName() {
        return $this->_controllerName;
    }

    protected function setControllerName( $_controllerName ) {
        $this->_controllerName = $_controllerName;
    }

    public function getActionName() {
        return $this->_actionName;
    }

    protected function setActionName( $_actionName ) {
        $this->_actionName = $_actionName;
    }

    public function getRequestParams() {
        return $this->requestParams;
    }

    protected function setRequestParams( $requestParams ) {
        $this->requestParams = $requestParams;
    }

    protected function addRequestParam( $requestParam ) {
        $this->requestParams[] = $requestParam;
    }

    public abstract function processRequestUri( $uri, $method, $userRole, $postData );
}