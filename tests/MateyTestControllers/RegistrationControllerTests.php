<?php

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 19.10.16.
 * Time: 17.14
 */
class RegistrationControllerTests extends \AuthBucket\OAuth2\Tests\WebTestCase
{

    public function goodUserRegistration() {

        $options = [
            'form_params'   => array(
                'email' => 'marko@123.com',
                'password' => 'nekipassword',
                'first_name' => "Marko",
                'last_name' => "Ognjenovic"
            ),
        ];
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', '/register/user', $options);

        if($response->getStatusCode() == 200) return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            "good_user_registration" => "OK"
        ), 200);


    }

}