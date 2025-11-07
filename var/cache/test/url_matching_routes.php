<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/login' => [[['_route' => 'api_login', '_controller' => 'App\\Controller\\AuthController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/user/create' => [[['_route' => 'api_user_create', '_controller' => 'App\\Controller\\AuthController::create'], null, ['POST' => 0], null, false, false, null]],
        '/api/driver' => [[['_route' => 'api_driver_list', '_controller' => 'App\\Controller\\DriverController::list'], null, ['GET' => 0], null, false, false, null]],
        '/api/infractions' => [
            [['_route' => 'api_infractions_create', '_controller' => 'App\\Controller\\InfractionController::create'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'api_infractions_list', '_controller' => 'App\\Controller\\InfractionController::list'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/team' => [[['_route' => 'api_team_list', '_controller' => 'App\\Controller\\TeamController::list'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api/(?'
                    .'|driver/([^/]++)(*:30)'
                    .'|team/([^/]++)/drivers(?'
                        .'|(*:61)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        30 => [[['_route' => 'api_driver_get', '_controller' => 'App\\Controller\\DriverController::get'], ['id'], ['GET' => 0], null, false, true, null]],
        61 => [
            [['_route' => 'api_team_drivers_list', '_controller' => 'App\\Controller\\TeamController::listDrivers'], ['id'], ['GET' => 0], null, false, false, null],
            [['_route' => 'api_team_drivers_update', '_controller' => 'App\\Controller\\TeamController::updateDrivers'], ['id'], ['PATCH' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
