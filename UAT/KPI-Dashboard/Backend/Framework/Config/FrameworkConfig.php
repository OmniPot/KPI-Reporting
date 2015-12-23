<?php

namespace KPIReporting\Framework\Config;

use KPIReporting\Config\AppConfig;

class FrameworkConfig {

    const PHP_EXTENSION = '.php';
    const PARENT_DIR_PREFIX = '..\\';

    const APP_STRUCTURE_NAME = '../appStructure.php';
    const APP_STRUCTURE_CONFIG_RENEW_TIME = 'PT10S';

    const DEFAULT_AREA = AppConfig::DEFAULT_AREA;
    const AREA_SUFFIX = 'Area\\';
    const DEFAULT_CONTROLLER = AppConfig::DEFAULT_CONTROLLER;
    const CONTROLLER_SUFFIX = AppConfig::CONTROLLER_SUFFIX;
    const DEFAULT_ACTION = AppConfig::DEFAULT_ACTION;

    const VENDOR_NAMESPACE = 'KPIReporting\\';
    const CONTROLLERS_NAMESPACE = 'Controllers\\';
    const AREAS_NAMESPACE = 'Areas\\';
    const REPOSITORIES_NAMESPACE = 'Repositories\\';
}