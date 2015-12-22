<?php

namespace KPIReporting\Config;

class RoutingConfig {

    private static $_customMappings = [ ];

    const ROUTING_TYPE = 'custom';

    const UNAUTHORIZED_REDIRECT = 'user/login';
    const AUTHORIZED_REDIRECT = 'profiles/me';

    public static function getCustomMappings() {

        // Example custom route
        self::$_customMappings[] = [
            'customRoute' => [
                'uri' => 'projectss',
                'bindingParams' => [ ]
            ],
            'method' => 'GET',
            'authorize' => '1',
            'admin' => '',
            'defaultRoute' => 'main/projects/getAll'
        ];

        return self::$_customMappings;
    }
}