<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.10.16.
 * Time: 01.08
 */

namespace App\Controllers;


use App\Services\BaseService;
use App\Services\LoginService;
use AuthBucket\OAuth2\Controller\OAuth2Controller;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use GuzzleHttp\Client;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class LoginController extends AbstractController
{

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function loginAction (Application $app, Request $request) {

        $userType = $request->request->get("user_type");

        if ($userType == "fb") return $this->loginFbUser($app, $request);
        else if ($userType == "standard") return $this->loginStandardUser($app, $request);

        throw new InvalidRequestException([
            'error_description' => 'The request includes an invalid parameter value.',
        ]);

    }

    public function loginStandardUser (Application $app, Request $request) {

        $username = $request->get("username");
        $password = $request->get("password");

        $client = new Client();
        $response = $client->request('POST', 'http://localhost/matey-oauth2/web/index.php/api/oauth2/token', [
            'form_params'   => array(
                'username' => $username,
                'password' => $password,
                'grant_type' => 'password'
            ),
            'auth' => [$app['client_id'], $app['client_secret']]
        ]);

        $data = $response->getBody();

        return new JsonResponse($data, 200);

            // fetching data for login from request
            $deviceId = $request->request->get("device_id");
            $username = $request->request->get("username");

            // logging in the user
            try {

                $userData = $this->service->loginUser($deviceId, $username);

            } catch (\Exception $e) {

                throw new InvalidRequestException([
                    'error_description' => 'The request includes an invalid parameter value.',
                ]);

            }

        $parameters['user_id'] = $userData['id_user'];
        $parameters['first_name'] = $userData['first_name'];
        $parameters['last_name'] = $userData['last_name'];

        return JsonResponse::create($parameters, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);

    }

    public function loginFbUser (Application $app, Request $request) {
    }

}