<?php

namespace KPIReporting\Framework\Helpers;

use KPIReporting\Framework\Config\FrameworkConfig;

class DirectoryHelper {

    public static function getControllerPath( $areaName, $controllerName ) {
        if ( !$areaName ) {
            throw new \Exception( 'Invalid area name' );
        }

        if ( !$controllerName ) {
            throw new \Exception( 'Invalid controller name' );
        }

        $fullControllerName = FrameworkConfig::VENDOR_NAMESPACE;
        if ( $areaName != FrameworkConfig::DEFAULT_AREA ) {
            $fullControllerName .= FrameworkConfig::AREAS_NAMESPACE . $areaName . FrameworkConfig::AREA_SUFFIX;
        }

        $fullControllerName .= FrameworkConfig::CONTROLLERS_NAMESPACE . $controllerName . FrameworkConfig::CONTROLLER_SUFFIX;

        return $fullControllerName;
    }

    public static function getControllerName( $controllerFullPath ) {
        if ( !$controllerFullPath ) {
            throw new \Exception( 'Invalid full controller name' );
        }

        $controllerNameRegex =
            '/' . FrameworkConfig::CONTROLLERS_NAMESPACE . '\(.*)' . FrameworkConfig::CONTROLLER_SUFFIX . '/';

        preg_match( $controllerNameRegex, $controllerFullPath, $controllerMatches );
        $controller = lcfirst( $controllerMatches[ 1 ] );

        return $controller;
    }
}