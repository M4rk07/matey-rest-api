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

    public function loginAction (Application $app, Request $request) {

        $loginType = $request->request->get("login_type");

        if ($loginType == "fb") return $this->loginFbUser($app, $request);
        else if ($loginType == "standard") return $this->loginStandardUser($app, $request);

        throw new InvalidRequestException([
            'error_description' => 'The request includes an invalid parameter value.',
        ]);

    }

    public function loginStandardUser (Application $app, Request $request) {

        $email = $request->request->get("email");
        $deviceId = $request->request->get("device_id");

        // store user login information
        // on which device he is logging in
        $userData = $this->storeUserLoginInfo($deviceId, $email);

        // make response
        //$parameters['token_data'] = $tokenData;

        return JsonResponse::create($userData, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);

    }

    public function loginFbUser (Application $app, Request $request) {
    }

    public function storeUserLoginInfo ($deviceId, $email) {

        // record that user is logged on device
        try {
            $userData = $this->service->storeLoginRecord($deviceId, $email);
        } catch (\Exception $e) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        return $userData;

    }

}