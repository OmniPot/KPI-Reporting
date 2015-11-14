<?php

namespace KPIReporting\Framework\Config;

use KPIReporting\Config\AppConfig;
use KPIReporting\Framework\Helpers\AnnotationParser;
use KPIReporting\Framework\Helpers\DirectoryHelper;
use KPIReporting\Framework\Helpers\FileHelper;

class AppStructureConfig {

    private static $_instance;

    private $_appStructure = [ ];
    private $_actionsArray = [ ];

    private $_actionDataTemplate = [
        'customRoute' => [
            'uri' => '',
            'uriParams' => [ ],
            'bindingParams' => [ ]
        ],
        'method' => 'GET',
        'authorize' => false,
        'admin' => false,
        'defaultRoute' => ''
    ];

    private $_propertyDataTemplate = [
        'required' => false
    ];

    // Properties
    private function __construct() {
    }

    public function getAppStructure() {
        return $this->_appStructure;
    }

    private function setAppStructure( $_appStructure ) {
        $this->_appStructure = $_appStructure;
    }

    public function getActionsArray() {
        return $this->_actionsArray;
    }

    private function setActionsArray( $_actionsArray ) {
        $this->_actionsArray = $_actionsArray;
    }

    // Methods
    public function setupConfig() {
        if ( !file_exists( FrameworkConfig::APP_STRUCTURE_NAME ) ||
            !is_readable( FrameworkConfig::APP_STRUCTURE_NAME )
        ) {
            $this->writeConfig();
        }
        else {
            include_once FrameworkConfig::APP_STRUCTURE_NAME;

            if ( !isset( $expires ) || !isset( $appStructure ) || !isset( $actionsStructure ) ) {
                throw new \Exception( 'App structure config contains invalid information' );
            }

            $now = new \DateTime( 'now', new \DateTimeZone( AppConfig::TIME_ZONE ) );
            $expires = new \DateTime( $expires, new \DateTimeZone( AppConfig::TIME_ZONE ) );

            if ( $now->getTimestamp() > $expires->getTimestamp() ) {
                unlink( FrameworkConfig::APP_STRUCTURE_NAME );
                $this->writeConfig();
            }

            $this->setAppStructure( $appStructure );
            $this->setActionsArray( $actionsStructure );
        }
    }

    private function writeConfig() {
        $this->registerAppStructure();

        $content = FileHelper::writeFile( $this->getAppStructure(), $this->getActionsArray() );
        file_put_contents( FrameworkConfig::APP_STRUCTURE_NAME, $content );
    }

    private function registerAppStructure() {
        $this->_appStructure[ ucfirst( FrameworkConfig::DEFAULT_AREA ) ] = [ ];
        $this->registerAreaControllers(
            FrameworkConfig::PARENT_DIR_PREFIX,
            FrameworkConfig::DEFAULT_AREA
        );

        $globParam = FrameworkConfig::PARENT_DIR_PREFIX
            . FrameworkConfig::AREAS_NAMESPACE
            . '*' . FrameworkConfig::AREA_SUFFIX;

        foreach ( glob( $globParam ) as $areaPath ) {
            if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                $replaceable = [
                    FrameworkConfig::PARENT_DIR_PREFIX,
                    FrameworkConfig::AREAS_NAMESPACE,
                    FrameworkConfig::AREA_SUFFIX
                ];
                $areaName = str_replace( $replaceable, '', $areaPath );
                $this->_appStructure[ $areaName ] = [ ];

                $this->registerAreaControllers( $areaPath, $areaName );
            }
            else {
                throw new \Exception( 'Directory not found: ' . $areaPath );
            }
        }
    }

    private function registerAreaControllers( $areaPath, $areaName ) {
        $globParam = $areaPath
            . FrameworkConfig::CONTROLLERS_NAMESPACE
            . '*' . FrameworkConfig::PHP_EXTENSION;

        foreach ( glob( $globParam ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = FrameworkConfig::VENDOR_NAMESPACE .
                    str_replace(
                        [
                            FrameworkConfig::PARENT_DIR_PREFIX,
                            FrameworkConfig::PHP_EXTENSION
                        ],
                        '',
                        $controllerPath
                    );
                $this->_appStructure[ $areaName ][ $fullPath ] = [ ];
                $this->registerControllersActions( $areaName, $fullPath );
            }
            else {
                throw new \Exception( 'File not found or is not readable: ' . $controllerPath );
            }
        }
    }

    private function registerControllersActions( $areaName, $fullPath ) {
        $class = new \ReflectionClass( $fullPath );
        $actions = $class->getMethods();

        foreach ( $actions as $action ) {
            if ( !$action->isPublic() || $action->name == '__construct' ) {
                continue;
            }

            $this->_appStructure[ $areaName ][ $fullPath ][ $action->name ] = [ ];
            $realRoute = $this->validateRouteUri( $areaName, $fullPath, $action->name );

            $actionDoc = AnnotationParser::getDoc( $action );
            $actionData = AnnotationParser::parseDoc( $actionDoc, $this->_actionDataTemplate, 'action' );
            $actionData[ 'defaultRoute' ] = $realRoute;

            $actionData = $this->registerActionBindings( $action, $actionData );

            $this->_appStructure[ $areaName ][ $fullPath ][ $action->name ] = $actionData;
            $this->_actionsArray[ $action->name ] = $this->_appStructure[ $areaName ][ $fullPath ][ $action->name ];
        }
    }

    private function registerActionBindings( $action, $actionData ) {
        foreach ( $action->getParameters() as $param ) {
            if ( $param->getClass() ) {
                $className = $param->getClass()->name;
                $actionData[ 'customRoute' ][ 'bindingParams' ][ $className ] = [ ];
                $paramReflection = new \ReflectionClass( $className );

                foreach ( $paramReflection->getProperties() as $property ) {
                    $propertyData = AnnotationParser::parseDoc(
                        $property->getDocComment(),
                        $this->_propertyDataTemplate,
                        'property'
                    );
                    $actionData[ 'customRoute' ][ 'bindingParams' ][ $className ][ $property->name ] = $propertyData;
                }
            }
        }

        return $actionData;
    }

    private function validateRouteUri( $areaName, $fullControllerName, $actionName ) {
        if ( !isset( $this->getAppStructure()[ $areaName ] ) ) {
            throw new \Exception( "Area: $areaName not found." );
        }

        if ( !isset( $this->getAppStructure()[ $areaName ][ $fullControllerName ] ) ) {
            throw new \Exception( "Controller: $fullControllerName not found" );
        }

        if ( !isset( $this->getAppStructure()[ $areaName ][ $fullControllerName ][ $actionName ] ) ) {
            throw new \Exception(
                "Controller: $fullControllerName contains no method: $actionName"
            );
        }

        $controller = DirectoryHelper::getControllerName( $fullControllerName );
        $area = strtolower( $areaName );
        $route = "$area/$controller/$actionName";

        return $route;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}