<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 22.55
 */

$app['security.firewalls'] = [
    'api_resource' => [
        'pattern' => '^/api/v1',
        'oauth2_resource' => [
            'resource_type' => 'debug_endpoint',
            'scope' => [''],
            'options' => [
                'debug_endpoint' => 'http://localhost/matey-oauth2/web/index.php/api/oauth2/debug',
                'cache' => false,
            ],
        ],
    ],
];