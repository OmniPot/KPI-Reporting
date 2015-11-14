<?php

namespace KPIReporting\Framework\Routers;

use KPIReporting\Config\RoutingConfig;
use KPIReporting\Exceptions\ApplicationException;
use KPIReporting\Framework\Helpers\DirectoryHelper;

class Router extends BaseRouter {

    const STRING_VALIDATION_REGEX = '([a-zA-Z\_\-]+)';
    const INT_VALIDATION_REGEX = '([0-9]+)';
    const MIXED_VALIDATION_REGEX = '([a-zA-Z0-9\_\,\-]+)';

    private $_appStructure;
    private $_actionsArray;
    private $_customMappings;

    public function __construct( $_appStructure, $_actionsArray, $_customMappings ) {
        parent::__construct();

        $this->setAppStructure( $_appStructure );
        $this->setActionsArray( $_actionsArray );
        $this->setCustomMappings( $_customMappings );
    }

    // Properties
    public function getAppStructure() {
        return $this->_appStructure;
    }

    public function setAppStructure( $appStructure ) {
        $this->_appStructure = $appStructure;
    }

    public function getActionsArray() {
        return $this->_actionsArray;
    }

    public function setActionsArray( $actionsArray ) {
        $this->_actionsArray = $actionsArray;
    }

    public function getCustomMappings() {
        return $this->_customMappings;
    }

    public function setCustomMappings( $customMappings ) {
        $this->_customMappings = $customMappings;
    }

    // Methods
    public function processRequestUri( $uri, $method, $userRole, $postData ) {
        if ( RoutingConfig::ROUTING_TYPE != 'default' ) {
            $result = $this->processCustomRequestUri( $uri, $method, $userRole, $postData );
        } else {
            $result = $this->processDefaultRequestUri( $uri, $postData );
        }

        return $result;
    }

    private function processDefaultRequestUri( $uri, $postData ) {
        $uriParts = explode( '/', trim( $uri, ' ' ) );
        if ( count( $uriParts ) < 3 ) {
            http_response_code( 404 );
            die( 'No action found' );
        }

        $area = ucfirst( $uriParts[ 0 ] );
        $controller = ucfirst( $uriParts[ 1 ] );
        $action = $uriParts[ 2 ];
        $params = array_slice( $uriParts, 3 );

        $fullControllerName = DirectoryHelper::getControllerPath( $area, $controller );
        if ( !isset( $this->getAppStructure()[ $area ][ $fullControllerName ][ $action ] ) ) {
            http_response_code( 404 );
            die( 'No action found' );
        }

        $this->setAreaName( $area );
        $this->setControllerName( ucfirst( $controller ) );
        $this->setActionName( $action );
        $this->setRequestParams( $params );

        if ( !$this->validatePostData( $postData ) ) {
            http_response_code( 400 );
            die( 'Invalid data supplied' );
        }

        return new RequestUriResult(
            $this->getAreaName(),
            $this->getControllerName(),
            $this->getActionName(),
            $this->getRequestParams()
        );
    }

    private function processCustomRequestUri( $uri, $method, $userRole, $postData ) {
        $uri = $this->matchCustomRoutes( $this->getActionsArray(), $uri, $method, $userRole );
        $uri = $this->matchCustomRoutes( $this->getCustomMappings(), $uri, $method, $userRole );

        return $this->processDefaultRequestUri( $uri, $postData );
    }

    private function matchCustomRoutes( $collection, $uri, $method, $userRole ) {
        foreach ( $collection as $key => $value ) {
            $customUriParts = explode( '/', rtrim( $value[ 'customRoute' ][ 'uri' ], '/ ' ) );
            $urlRegex = $this->getUrlRegex( $customUriParts );

            preg_match( $urlRegex, $uri, $matches );

            if ( !empty( $matches[ 0 ] ) ) {
                if ( $method != $value[ 'method' ] ) {
                    $invalidMethod = true;
                    continue;
                }
                if ( !$this->validateActionAuthorization( $userRole, $value[ 'authorize' ], $value[ 'admin' ] ) ) {
                    $unauthorized = true;
                    continue;
                }

                $uri = $value[ 'defaultRoute' ];
                foreach ( array_slice( $matches, 1 ) as $match ) {
                    $uri .= '/' . $match;
                }

                return $uri;
            }
        }

        if ( isset( $invalidMethod ) ) {
            throw new ApplicationException( 'Invalid action method', 400 );
        }

        if ( isset( $unauthorized ) ) {
            throw new ApplicationException( 'Unauthorized', 403 );
        }

        return $uri;
    }

    private function getUrlRegex( $collection ) {
        $regex = "/^";

        foreach ( $collection as $part ) {
            switch ( $part ) {
                case 'int' :
                    $regex .= self::INT_VALIDATION_REGEX;
                    break;
                case 'string' :
                    $regex .= self::STRING_VALIDATION_REGEX;
                    break;
                case 'mixed' :
                    $regex .= self::MIXED_VALIDATION_REGEX;
                    break;
                default :
                    $regex .= $part;
                    break;
            }

            $regex .= "\\/";
        }

        $regex = rtrim( $regex, '\\/' ) . "$/";

        return $regex;
    }

    private function validatePostData( $postData ) {
        $actionRoute = $this->getActionsArray()[ $this->getActionName() ][ 'customRoute' ];

        if ( !empty( $actionRoute[ 'bindingParams' ] ) ) {
            $bindings = $actionRoute[ 'bindingParams' ];

            foreach ( $bindings as $modelName => $properties ) {
                $bindingModel = new $modelName();

                foreach ( $properties as $propName => $restriction ) {
                    if ( $restriction[ 'required' ] && ( !isset( $postData->$propName ) || !$postData->$propName ) ) {
                        return false;
                    }

                    $bindingModel->$propName = isset( $postData->$propName ) ? $postData->$propName : null;
                }

                $this->addRequestParam( $bindingModel );
            }
        }

        return true;
    }

    private function validateActionAuthorization( $userRole, $requiredUser, $requiredAdmin ) {

        $requiredAuthLevel = 'guest';
        if ( $requiredUser ) {
            $requiredAuthLevel = 'user';
        }
        if ( $requiredAdmin ) {
            $requiredAuthLevel = 'admin';
        }

        if ( ( $requiredAuthLevel == 'admin' && $userRole != 'admin' ) ||
            ( $requiredAuthLevel == 'user' && ( $userRole != 'admin' && $userRole != 'user' ) )
        ) {
            return false;
        }

        return true;
    }
}