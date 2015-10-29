<?php

namespace Medieval\Config;

class AppConfig {

    const TIME_ZONE = 'Europe/Sofia';
    const APP_STRUCTURE_EXPIRES = 'expires';
    const APP_STRUCTURE = 'appStructure';
    const APP_ACTION_STRUCTURE = 'actionsStructure';

    const DEFAULT_AREA = 'Main';
    const DEFAULT_CONTROLLER = 'Home';
    const DEFAULT_ACTION = 'welcome';

    const AREA_SUFFIX = 'Area';
    const CONTROLLER_SUFFIX = 'Controller';

    const VENDOR_NAMESPACE = 'Medieval';
    const CONTROLLERS_NAMESPACE = 'Controllers';
    const AREAS_NAMESPACE = 'Areas';
    const REPOSITORIES_NAMESPACE = 'Repositories';
    const VIEW_MODELS_NAMESPACE = 'ViewModels';
    const VIEWS_NAMESPACE = 'Views';
}