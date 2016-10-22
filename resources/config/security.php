<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 22.55
 */
//$app['security.firewalls'] =[];

$app['security.firewalls'] = [
    'api_resource' => [
        'pattern' => '^/api/v1',
        'oauth2_resource' => [
            'resource_type' => 'debug_endpoint',
            'scope' => [''],
            'options' => [
                'debug_endpoint' => \App\Paths\Paths::DEBUG_ENDPOINT,
                'cache' => false,
            ],
        ],
    ],
];