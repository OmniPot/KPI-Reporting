<?php

namespace Medieval\Framework\Routers;

class RequestUriResult {

    private $_areaName;
    private $_controllerName;
    private $_actionName;
    private $_requestParams;

    public function __construct( $_areaName, $_controllerName, $_actionName, $_requestParams = [ ] ) {
        $this->setAreaName( $_areaName );
        $this->setControllerName( $_controllerName );
        $this->setActionName( $_actionName );
        $this->setRequestParams( $_requestParams );
    }

    public function getAreaName() {
        return $this->_areaName;
    }

    public function setAreaName( $areaName ) {
        $this->_areaName = $areaName;
    }

    public function getControllerName() {
        return $this->_controllerName;
    }

    public function setControllerName( $controllerName ) {
        $this->_controllerName = $controllerName;
    }

    public function getActionName() {
        return $this->_actionName;
    }

    public function setActionName( $actionName ) {
        $this->_actionName = $actionName;
    }

    public function getRequestParams() {
        return $this->_requestParams;
    }

    public function setRequestParams( $requestParams ) {
        $this->_requestParams = $requestParams;
    }
}