<?php
namespace KPIReporting\Framework;

use KPIReporting\Config\RoutingConfig;
use KPIReporting\Framework\Config\AppStructureConfig;
use KPIReporting\Framework\Config\DatabaseConfig;
use KPIReporting\Framework\Database\Database;
use KPIReporting\Framework\Routers\Router;

class App {
    /** @var $_instance \KPIReporting\Framework\App */
    private static $_instance = null;
    /** @var $_router Routers\Router */
    private $_router;
    /** @var $_frontController FrontController */
    private $_frontController;
    /** @var $_appStructureConfig Config\AppStructureConfig */
    private $_appStructureConfig;

    private function __construct() {
        $this->initAutoload();

        $this->_appStructureConfig = AppStructureConfig::getInstance();
        $this->_appStructureConfig->setupConfig();

        $this->_router = new Router(
            $this->_appStructureConfig->getAppStructure(),
            $this->_appStructureConfig->getActionsArray(),
            RoutingConfig::getCustomMappings()
        );
        $this->_frontController = FrontController::getInstance( $this->_router );
    }

    public function start() {
        Database::setInstance(
            DatabaseConfig::DB_INSTANCE_NAME,
            DatabaseConfig::DB_DRIVER,
            DatabaseConfig::DB_USERNAME,
            DatabaseConfig::DB_PASSWORD,
            DatabaseConfig::DB_NAME,
            DatabaseConfig::DB_HOST
        );

        $this->_frontController->dispatch();
    }

    public function initAutoLoad() {
        spl_autoload_register(
            function ( $class ) {
                $separatorReplaced = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $class );
                $classPath = str_replace( 'KPIReporting', '..', $separatorReplaced . '.php' );
                if ( file_exists( $classPath ) && is_readable( $classPath ) ) {
                    require_once $classPath;
                } else {
                    throw new \Exception( 'File not found or is not readable: ' . $classPath );
                }
            }
        );
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}