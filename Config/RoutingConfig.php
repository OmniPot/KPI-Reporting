<?php

namespace Medieval\Config;

class RoutingConfig {

    private static $_customMappings = [ ];

    const ROUTING_TYPE = 'custom';

    const UNAUTHORIZED_REDIRECT = 'user/login';
    const AUTHORIZED_REDIRECT = 'profiles/me';

    public static function getCustomMappings() {

        // Example custom route
        self::$_customMappings[] = [
            'customRoute' => [
                'uri' => 'projects',
                'uriParams' => [ ],
                'bindingParams' => [ ]
            ],
            'method' => 'GET',
            'authorize' => '1',
            'admin' => '1',
            'defaultRoute' => 'main/projects/all'
        ];

        return self::$_customMappings;
    }
}