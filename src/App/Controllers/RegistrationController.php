<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 23.13
 */

namespace App\Controllers;


use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\Request;

class RegistrationController extends AbstractController
{

    public function registerStandardUserAction (Request $request) {

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $first_name = $request->request->get('first_name');
        $last_name = $request->request->get('last_name');
        $birth_year = $request->request->get('birth_year');

        // register user on authorization server
        $client = new Client();
        $response = $client->request('POST', 'http://localhost/matey-oauth2/web/index.php/api/oauth2/register/user', [
            'form_params'   => array(
                'username' => $email,
                'password' => $password
            ),
        ]);
        $responseData = json_decode($response->getBody());
        $userId = $responseData->user_id;

        $this->storeUserData($userId, $email, $first_name, $last_name, $birth_year);

        return new JsonResponse(array('success' => true), 200);

    }

    public function storeUserData($userId, $email, $first_name, $last_name, $birth_year) {

        $this->service->storeUserData($userId, $email, $first_name, $last_name, $birth_year);

    }

}