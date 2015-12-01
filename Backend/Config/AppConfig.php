<?php

namespace KPIReporting\Config;

class AppConfig {

    const TIME_ZONE = 'Europe/Sofia';
    const APP_STRUCTURE_EXPIRES = 'expires';
    const APP_STRUCTURE = 'appStructure';
    const APP_ACTION_STRUCTURE = 'actionsStructure';

    const DEFAULT_AREA = 'Main';
    const DEFAULT_CONTROLLER = 'Profiles';
    const DEFAULT_ACTION = 'me';

    const AREA_SUFFIX = 'Area';
    const CONTROLLER_SUFFIX = 'Controller';

    const VENDOR_NAMESPACE = 'KPIReporting';
    const CONTROLLERS_NAMESPACE = 'Controllers';
    const AREAS_NAMESPACE = 'Areas';
    const REPOSITORIES_NAMESPACE = 'Repositories';

    const PERCENTAGE_TOLERANCE_DAYS_CALCULATION = 5;
    const PERCENTAGE_TOLERANCE_TEST_CASES_PER_DAY = 10;
}