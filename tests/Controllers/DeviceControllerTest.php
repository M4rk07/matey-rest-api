<?php

namespace App\Tests\Controllers;
use App\Paths\Paths;
use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.3.17.
 * Time: 01.05
 */
class DevicesControllerTest extends AbstractControllerTest
{

    public function test_RegisterDevice()
    {
        $gcm = 'gfmdskjankjnfalsjnlasifd';
        $response = $this->client->post('devices', [
            'form_params' => [
                'gcm' => $gcm
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('device_id', $data);
        $this->assertArrayHasKey('device_secret', $data);
        $this->assertEquals($gcm, $data['gcm']);

        return $data;
    }

    /**
     * @depends test_RegisterDevice
     */
    public function test_UpdateDevice ($data) {
        $gcm = 'smkjfnasdjknkdsanfk';

        $response = $this->client->post('api/v1/devices/'.$data['device_id'], [
            'form_params' => [
                'old_gcm' => $data['gcm'],
                'gcm' => $gcm
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('device_id', $data);
        $this->assertEquals($gcm, $data['gcm']);
    }

    /**
     * @dataProvider deviceRegInvalidParamProvider
     */
    public function test_RegisterDevice_InvalidParam($gcm) {

        $response = $this->client->post('devices', [
            'form_params' => [
                'gcm' => $gcm
            ]
        ]);


        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertEquals('invalid_request', $data['error']);
    }

    public function deviceRegInvalidParamProvider() {
        return [
            'blank_gcm'  => [""],
            'short_gcm' => ["marko@g"]
        ];
    }

}