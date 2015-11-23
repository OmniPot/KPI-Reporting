<?php

namespace KPIReporting\Framework;

use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\Helpers\DirectoryHelper;
use KPIReporting\Framework\Routers\RequestUriResult;
use KPIReporting\Framework\Routers\Router;

class FrontController {

    private static $_instance = null;
    private $_requestUri;
    private $_requestMethod;
    private $_userRole;
    private $_postData;

    /** @var $_controller BaseController */
    private $_controller;
    /** @var $_router Routers\Router */
    private $_router;
    /** @var $_uriParsedResult Routers\RequestUriResult */
    private $_uriParsedResult;

    private function __construct( $router ) {
        $this->setRouter( $router );
        $this->setRequestUri( $_GET[ 'uri' ] );
        $this->setRequestMethod( $_SERVER[ 'REQUEST_METHOD' ] );
        $this->setUserRole( isset( $_SESSION[ 'role' ] ) ? $_SESSION[ 'role' ] : 'guest' );

        $this->setPostData( json_decode( file_get_contents( 'php://input' ) ) );
    }

    public function getRequestUri() {
        return $this->_requestUri;
    }

    private function setRequestUri( $requestUri ) {
        $this->_requestUri = $requestUri;
    }

    public function getRequestMethod() {
        return $this->_requestMethod;
    }

    private function setRequestMethod( $requestMethod ) {
        $this->_requestMethod = $requestMethod;
    }

    public function getUserRole() {
        return $this->_userRole;
    }

    private function setUserRole( $userRole ) {
        $this->_userRole = $userRole;
    }

    public function getController() {
        return $this->_controller;
    }

    private function setController( $controller ) {
        $this->_controller = $controller;
    }

    public function getRouter() {
        return $this->_router;
    }

    private function setRouter( $router ) {
        $this->_router = $router;
    }

    public function getUriParsedResult() {
        return $this->_uriParsedResult;
    }

    private function setUriParsedResult( $uriParsedResult ) {
        $this->_uriParsedResult = $uriParsedResult;
    }

    public function getPostData() {
        return $this->_postData;
    }

    private function setPostData( $postData ) {
        $this->_postData = $postData;
    }

    public function dispatch() {
        try {
            $uriParseResults = $this->getRouter()->processRequestUri(
                $this->getRequestUri(),
                $this->getRequestMethod(),
                $this->getUserRole(),
                $this->getPostData()
            );

            $this->setUriParsedResult( $uriParseResults );
            $this->initController( $this->getUriParsedResult() );

            $actionResultData = call_user_func_array(
                [
                    $this->getController(),
                    $this->getUriParsedResult()->getActionName()
                ],
                $this->getUriParsedResult()->getRequestParams()
            );

            header( 'Content-Type: application/json' );
            die( json_encode( $actionResultData, JSON_UNESCAPED_UNICODE ) );
        }
        catch ( ApplicationException $exception ) {
            http_response_code( $exception->getCode() );
            die( $exception->getMessage() );
        }
        catch ( \Exception $exception ) {
            http_response_code( $exception->getCode() );
            die( $exception->getMessage() );
        }
    }

    private function initController( RequestUriResult $requestUriResult ) {
        if ( !$requestUriResult ) {
            throw new \Exception( 'Url parse error' );
        }

        $fullControllerName = DirectoryHelper::getControllerPath(
            $requestUriResult->getAreaName(),
            $requestUriResult->getControllerName()
        );

        $controller = new $fullControllerName( $requestUriResult );
        $this->setController( $controller );
    }

    /**
     * @param Router $router
     * @return FrontController
     */
    public static function getInstance( Router $router ) {
        if ( self::$_instance == null ) {
            self::$_instance = new self( $router );
        }

        return self::$_instance;
    }
}