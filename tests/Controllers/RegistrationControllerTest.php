<?php

namespace App\Tests\Controllers;
use App\Paths\Paths;
use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 7.11.16.
 * Time: 23.52
 */
class RegistrationControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider registrationValuesProvider
     */
    public function testRegisterInvalidParamsValues($email, $password, $firstName, $lastName, $expectedCode, $expectedError) {

        $parameters = [
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];

        $client = new Client();
        $response = $client->request('POST', 'http://localhost/matey-api/web/index.php/register/standard', [
            'form_params' => $parameters,
            'http_errors' => false
        ]);
        $this->assertSame($expectedCode, $response->getStatusCode());
        if($expectedError != null) {
            $response = json_decode($response->getBody());
            $this->assertSame($expectedError, $response->error);
        }
    }

    public function registrationValuesProvider() {
        return [
            'wrong email format'  => ["marko", "marko", "Marko", "Ognjenovic", 400, 'invalid_request'],
            'email with space in middle' => ["marko@gmail.com s", "marko123", "Marko", "Ognjenovic", 400, 'invalid_request'],
            'short password' => ["marko@gmail.com", "ma", "Marko", "Ognjenovic", 400, 'invalid_request']
        ];
    }

    /**
     * @depends testRegisterParamsValues
     */
    /*
    public function testRegisterAgainUser () {

        $parameters = [
            'email' => 'marko@gmail.com',
            'password' => 'marko',
            'first_name' => 'Marko',
            'last_name' => 'Ognjenovic'
        ];

        $client = new Client();
        $response = $client->request('POST', 'http://localhost/matey-api/web/index.php/register/standard', [
            'form_params' => $parameters,
            'http_errors' => false
        ]);


        $this->assertSame(409, $response->getStatusCode());
        $response = json_decode($response->getBody());
        $this->assertSame('full_reg', $response->error);

    }*/

}