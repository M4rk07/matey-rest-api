<?php

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 19.10.16.
 * Time: 16.38
 */
class MateyTestControllers extends \AuthBucket\OAuth2\Tests\WebTestCase
{
    const BASE_API_URL = "http://localhost/matey-api/index.php";

    public function testUserRegistration() {
        // register user on authorization server
        $client = new \GuzzleHttp\Client();
        $client->request('POST', self::BASE_API_URL.'/register/user', [
            'form_params'   => array(
                'email' => '',
                'password' => '',
                'first_name' => '',
                'last_name' => ''
            ),
        ]);
    }

}