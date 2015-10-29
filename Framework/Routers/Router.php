<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\RoutingConfig;
use Medieval\Framework\Helpers\DirectoryHelper;

class Router extends BaseRouter {

    const STRING_VALIDATION_REGEX = '/^[a-zA-Z\_\-]+$/';
    const INT_VALIDATION_REGEX = '/^[0-9]+$/';
    const MIXED_VALIDATION_REGEX = '/^[a-zA-Z0-9\_\,\-]+$/';

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
        }
        else {
            $result = $this->processDefaultRequestUri( $uri, $postData );
        }

        return $result;
    }

    private function processDefaultRequestUri( $uri, $postData ) {
        $uriParts = explode( '/', trim( $uri, ' ' ) );
        if ( count( $uriParts ) < 3 ) {
            throw new \Exception( 'No valid route found', 404 );
        }

        $area = ucfirst( $uriParts[ 0 ] );
        $controller = ucfirst( $uriParts[ 1 ] );
        $action = $uriParts[ 2 ];
        $params = array_slice( $uriParts, 3 );

        $fullControllerName = DirectoryHelper::getControllerPath( $area, $controller );
        if ( !isset( $this->getAppStructure()[ $area ][ $fullControllerName ][ $action ] ) ) {
            throw new \Exception( 'No valid route found', 404 );
        }

        $this->setAreaName( $area );
        $this->setControllerName( ucfirst( $controller ) );
        $this->setActionName( $action );
        $this->setRequestParams( $params );

        if ( !$this->validatePostData( $postData ) ) {
            throw new \Exception( 'Invalid data supplied' );
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
        $uri = rtrim( $uri, '/ ' );
        $uriParts = explode( '/', $uri );

        foreach ( $collection as $key => $value ) {

            $customUriParts = explode( '/', rtrim( $value[ 'customRoute' ][ 'uri' ], '/ ' ) );
            $defaultUriParts = explode( '/', rtrim( $value[ 'defaultRoute' ], '/ ' ) );

            $customUriMatch = array_slice( $uriParts, 0, count( $customUriParts ) ) == $customUriParts;
            $defaultUriMatch = array_slice( $uriParts, 0, count( $defaultUriParts ) ) == $defaultUriParts;

            if ( $customUriMatch || $defaultUriMatch ) {

                if ( $method != $value[ 'method' ] ) {
                    $invalidMethod = true;
                    continue;
                }

                $customRequestParams = array_slice( $uriParts, count( $customUriParts ), count( $uriParts ) );
                $defaultRequestParams = array_slice( $uriParts, count( $defaultUriParts ), count( $uriParts ) );

                if ( !$this->validateRequestParams( $customRequestParams, $value[ 'customRoute' ][ 'uriParams' ] ) &&
                    !$this->validateRequestParams( $defaultRequestParams, $value[ 'customRoute' ][ 'uriParams' ] )
                ) {
                    $invalidRequestParams = true;
                    continue;
                }

                if ( !$this->validateActionAuthorization( $userRole, $value[ 'authorize' ], $value[ 'admin' ] ) ) {
                    header( 'Location: /' . RoutingConfig::UNAUTHORIZED_REDIRECT );
                }

                $uri = $value[ 'defaultRoute' ];
                $uri .= !empty( $customRequestParams ) ? '/' . implode( '/', $customRequestParams ) : '';

                return $uri;
            }
        }

        if ( isset( $invalidMethod ) ) {
            throw new \Exception( 'Invalid action method' );
        }

        if ( isset( $invalidRequestParams ) ) {
            throw new \Exception( 'Invalid request parameters' );
        }

        return $uri;
    }

    private function validatePostData( $postData ) {
        $actionRoute = $this->getActionsArray()[ $this->getActionName() ][ 'customRoute' ];

        if ( !empty( $actionRoute[ 'bindingParams' ] ) ) {
            $bindings = $actionRoute[ 'bindingParams' ];

            foreach ( $bindings as $modelName => $properties ) {
                $bindingModel = new $modelName();

                foreach ( $properties as $propName => $restriction ) {
                    $presentInPostData = isset( $postData[ $propName ] ) && $postData[ $propName ];
                    if ( $restriction[ 'required' ] && !$presentInPostData ) {
                        return false;
                    }

                    $bindingModel->$propName = $postData[ $propName ];
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

    private function validateRequestParams( $requestParams, $paramTypes ) {
        if ( count( $requestParams ) != count( $paramTypes ) ) {
            return false;
        }

        for ( $i = 0; $i < count( $paramTypes ); $i++ ) {
            $typeMatches = false;

            switch ( $paramTypes[ $i ] ) {
                case 'string' :
                    $typeMatches = preg_match( self::STRING_VALIDATION_REGEX, $requestParams[ $i ] );
                    break;
                case 'int' :
                    $typeMatches = preg_match( self::INT_VALIDATION_REGEX, $requestParams[ $i ] );
                    break;
                case 'mixed' :
                    $typeMatches = preg_match( self::MIXED_VALIDATION_REGEX, $requestParams[ $i ] );
                    break;
            }

            if ( !$typeMatches ) {
                return false;
            };
        }

        return true;
    }
}