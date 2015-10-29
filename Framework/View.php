<?php

namespace Medieval\Framework;

use Medieval\Framework\Config\FrameworkConfig;

class View {

    public $_data = [ ];

    private static $_instance;
    private $_areaViewsDirectory;
    private $_sharedViewsDirectory;

    private $_layoutPartials = [ ];
    private $_renderedPartials = [ ];

    private function __construct() {
    }

    public function setAreaViewsDirectory( $_areaViewsDirectory ) {
        $this->_areaViewsDirectory = $_areaViewsDirectory;
    }

    public function setSharedViewsDirectory( $sharedViewsDirectory ) {
        $this->_sharedViewsDirectory = $sharedViewsDirectory;
    }

    public function appendToLayout( $layoutName, $templateName, $model = null ) {
        if ( !$layoutName ) {
            throw new \Exception( 'Invalid or no layout name provided' );
        }

        if ( !$templateName ) {
            throw new \Exception( 'Invalid or no template name provided' );
        }

        if ( !isset( $this->_layoutPartials[ $layoutName ] ) ) {
            $this->_layoutPartials[ $layoutName ] = [ ];
        }

        $this->appendHeader( $layoutName, $model );
        $this->appendFooter( $layoutName );

        $this->_layoutPartials[ $layoutName ][ $templateName ] = $model ? $model : null;
    }

    public function render( $layoutName, $returnAsString = false ) {
        if ( !$layoutName ) {
            throw new \Exception( 'Invalid or no layout name provided' );
        }

        foreach ( $this->_layoutPartials as $layoutName => $partials ) {
            foreach ( $partials as $partialName => $model ) {

                $this->validateModelType( $model, $partialName );
                $renderedPartial = $this->includeFile( $partialName, $model );

                if ( $renderedPartial ) {
                    $this->_renderedPartials[ $partialName ] = $renderedPartial;
                }
            }
        }

        if ( $returnAsString ) {
            return $this->includeFile( $layoutName );
        }
        else {
            echo $this->includeFile( $layoutName );
        }

        return null;
    }

    private function processFilePath( $fileName ) {
        $path = explode( '.', $fileName );
        $templateName = array_pop( $path );

        foreach ( $path as $partKey => $partValue ) {
            $path[ $partKey ] = ucfirst( $partValue );
        }

        $path[] = $templateName;
        $path = implode( DIRECTORY_SEPARATOR, $path );

        $areaPath = $this->_areaViewsDirectory . $path . FrameworkConfig::PHP_EXTENSION;
        $sharedPath = $this->_sharedViewsDirectory . $path . FrameworkConfig::PHP_EXTENSION;

        if ( !file_exists( $areaPath ) || !is_readable( $areaPath ) ) {
            if ( !file_exists( $sharedPath ) || !is_readable( $sharedPath ) ) {
                throw new \Exception( 'View ' . $fileName . ' not found', 404 );
            }

            return $sharedPath;
        }

        return $areaPath;
    }

    private function includeFile( $fileName, $model = null ) {
        $path = $this->processFilePath( $fileName );

        ob_start();
        include $path;

        return ob_get_clean();
    }

    private function renderPartial( $partialName ) {
        if ( isset( $this->_renderedPartials[ $partialName ] ) ) {
            return $this->_renderedPartials[ $partialName ];
        }

        return null;
    }

    private function validateModelType( $model, $partialName = null ) {
        $viewFile = $this->processFilePath( $partialName );
        $viewContent = file_get_contents( $viewFile );

        $typeRegex = ' /@var\s*.*\s+(' . FrameworkConfig::VENDOR_NAMESPACE . '\\.*?)\s+\s*.*/';
        preg_match( $typeRegex, $viewContent, $matches );

        $viewModelTypeExpected = !empty( $matches ) ? $matches[ 1 ] : null;
        if ( $viewModelTypeExpected ) {
            $modelType = $model ? get_class( $model ) : null;

            if ( $modelType != $viewModelTypeExpected ) {
                throw new \Exception( 'Invalid view model type supplied' );
            }
        }

        return $viewFile;
    }

    private function appendHeader( $layoutName, $model ) {
        if ( !isset( $this->_layoutPartials[ $layoutName ][ 'header' ] ) ) {
            $this->_layoutPartials[ $layoutName ][ 'header' ] = $model ? $model : null;
        }
    }

    private function appendFooter( $layoutName ) {
        if ( !isset( $this->_layoutPartials[ $layoutName ][ 'footer' ] ) ) {
            $this->_layoutPartials[ $layoutName ][ 'footer' ] = [ ];
        }
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}