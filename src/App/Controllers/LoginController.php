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

    public function loginUser (Application $app, Request $request) {

        $userType = $request->request->get("user_type");
        $grantType = $request->request->get("grant_type");

        if($grantType == "password") {

            if ($userType == "fb") return $this->loginFbUser($app, $request);
            else if ($userType == "standard") return $this->loginStandardUser($app, $request);

        }

        throw new InvalidRequestException([
            'error_description' => 'The request includes an invalid parameter value.',
        ]);

    }

    public function loginStandardUser (Application $app, Request $request) {


            // starting token endpoint with password grant_type
            $tokenController = $app['authbucket_oauth2.oauth2_controller'];
            $parameters = $tokenController->tokenAction($request);

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