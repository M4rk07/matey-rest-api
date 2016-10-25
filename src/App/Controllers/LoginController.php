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
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\TokenType\BearerTokenTypeHandler;
use AuthBucket\OAuth2\Validator\Constraints\Username;
use GuzzleHttp\Client;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class LoginController extends AbstractController
{

    public function loginAction (Request $request) {
        // fetch data from request
        $user_id = $request->request->get("user_id");
        $deviceId = $request->request->get("device_id");

        $this->validateNumericUnsigned($deviceId);

        $gcm = $this->service->getDeviceGcm($deviceId);
        // store user login information
        // on which device he is logging in
        $this->service->startTransaction();
        try {
            $userData = $this->service->storeLoginRecord($deviceId, $user_id, $gcm);
            $this->redisService->pushNewLoginGcm($userData['user_id'], $gcm);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return JsonResponse::create($userData, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);

    }

    // NOT FINISHED
    public function logoutAction (Request $request) {

        $user_id = $request->request->get('user_id');
        $deviceId = $request->request->get('device_id');

        $this->validateNumericUnsigned($deviceId);

        $gcm = $this->service->getDeviceGcm($deviceId);
        $this->service->startTransaction();
        try {
            $this->service->storeLogoutRecord($deviceId, $user_id);
            $this->redisService->deleteLoginGcm($user_id, $gcm);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk();

    }

}