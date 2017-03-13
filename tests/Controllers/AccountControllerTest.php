<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.12.16.
 * Time: 01.04
 */

namespace App\Tests\Controllers;


use GuzzleHttp\Client;

class AccountControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider registrationInvalidValuesProvider
     */
    public function testCreateAccountInvalidParams ($email, $password, $firstName, $lastName, $expectedCode, $expectedError) {
        $this->markTestSkipped('must be revisited.');

        $parameters = [
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];

        $client = new Client();
        $response = $client->request('POST', 'http://localhost/matey-api/web/index.php/users/accounts', [
            'form_params' => $parameters,
            'http_errors' => false
        ]);
        $this->assertSame($expectedCode, $response->getStatusCode());
        if($expectedError != null) {
            $response = json_decode($response->getBody());
            $this->assertSame($expectedError, $response->error);
        }
    }

    public function registrationInvalidValuesProvider() {
        return [
            'wrong email format'  => ["marko", "marko", "Marko", "Ognjenovic", 400, 'invalid_request'],
            'email with space in middle' => ["marko@gmail.com s", "marko123", "Marko", "Ognjenovic", 400, 'invalid_request'],
            'short password' => ["marko@gmail.com", "ma", "Marko", "Ognjenovic", 400, 'invalid_request'],
            'name with numbers' => ["marko@gmail.com", "ma", "Marko12", "Ognjenovic", 400, 'invalid_request'],
            'name with numbers space' => ["marko@gmail.com", "ma", "Marko 12", "Ognjenovic", 400, 'invalid_request'],
            'name with symbols' => ["marko@gmail.com", "ma", "&*@!#$()%.", "Ognjenovic", 400, 'invalid_request'],
            'name with dot' => ["marko@gmail.com", "ma", "Marko.", "Ognjenovic", 400, 'invalid_request'],
            'no value 1' => ["", "marko", "Marko", "Ognjenovic", 400, 'invalid_request'],
            'no value 2' => ["marko@gmail.com", "", "Marko", "Ognjenovic", 400, 'invalid_request'],
            'no value 3' => ["marko@gmail.com", "marko", "", "Ognjenovic", 400, 'invalid_request'],
            'no value 4' => ["marko@gmail.com", "marko", "Marko", "", 400, 'invalid_request']
        ];
    }

    /**
     * @dataProvider registrationValidValuesProvider
     */
    public function testCreateAccountValidParams ($email, $password, $firstName, $lastName, $expectedCode) {
        $this->markTestSkipped('must be revisited.');
        $parameters = [
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];

        $client = new Client();
        $response = $client->request('POST', 'http://localhost/matey-api/web/index.php/users/accounts', [
            'form_params' => $parameters,
            'http_errors' => false
        ]);
        $this->assertSame($expectedCode, $response->getStatusCode());
    }

    public function registrationValidValuesProvider() {
        return [
            'type 1'  => ["Mathias1@gmail.com", "marko", "Mathias", "d'Arras", 200],
            'type 2'  => ["Mathias2@gmail.com", "marko", "Martin", "Luther King, Jr.", 200],
            'type 3'  => ["Mathias3@gmail.com", "marko", "Hector", "Sausage-Hausen", 200],
            'type 4'  => ["Mathias4@gmail.com", "marko", "Marko", "Ognjenović", 200],
            'with whitespace'  => ["Mathias5@gmail.com", "marko", " Marko ", "Ognjenović", 200]
        ];
    }

}