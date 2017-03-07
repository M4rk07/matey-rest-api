<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 10.14
 */

namespace App\Handlers\Device;


use App\Security\SecretGenerator;
use App\Validators\DeviceId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraints\NotBlank;

class AndroidDeviceHandler extends AbstractDeviceHandler implements AndroidDeviceInterface
{
    public function createDevice(Request $request)
    {
        $gcm = $request->request->get("gcm");
        $errors = $this->validator->validate($gcm, [
            new NotBlank()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        $secretGenerator = new SecretGenerator();
        $deviceSecret = $secretGenerator->generateDeviceSecret();

        $deviceManager = $this->modelManagerFactory->getModelManager('device');
        $deviceClass = $deviceManager->getClassName();
        $device = new $deviceClass();

        $device->setDeviceSecret($deviceSecret)
            ->setGcm($gcm);

        /*
         * Creating new device
         */
        $device = $deviceManager->createModel($device);

        return new JsonResponse($device->getValuesAsArray(), 200);
    }

    public function updateDevice(Request $request, $deviceId)
    {
        $oldGcm = $request->request->get("old_gcm");
        $gcm = $request->request->get("gcm");

        $errors = $this->validator->validate($deviceId, [
            new NotBlank(),
            new DeviceId()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $device = $this->updateGcmById($deviceId, $gcm, $oldGcm);

        return new JsonResponse($device->getValuesAsArray(), 200);
    }

    public function loginOnDevice(Application $app, Request $request, $deviceId)
    {
        $userId = $request->request->get("user_id");

        $device = $this->getGcmById($deviceId);

        if(empty($device)) throw new ResourceNotFoundException();

        $loginManager = $this->modelManagerFactory->getModelManager('login');
        $loginClass = $loginManager->getClassName();
        $login = new $loginClass();

            if($request->getMethod() == "PUT") {

                $login->setUserId($userId)
                    ->setDeviceId($deviceId)
                    ->setStatus(1);
                $loginManager->createModel($login);

            }

            else if($request->getMethod() == "DELETE") {

                $login->setStatus(0);
                $loginManager->updateModel($login, array(
                    'device_id' => $deviceId,
                    'user_id' => $userId
                ));

            }

        return $app['matey.user_controller']->getUserAction($request, $userId);
    }

    public function getGcmById($deviceId)
    {
        $errors = $this->validator->validate($deviceId, [
            new NotBlank()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $deviceManager = $this->modelManagerFactory->getModelManager('device');

        $device = $deviceManager->readModelOneBy(array(
            'device_id' => $deviceId
        ));

        return $device;
    }

    public function updateGcmById($deviceId, $gcm, $oldGcm)
    {
        $errors = $this->validator->validate($oldGcm, [
            new NotBlank()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }
        $errors = $this->validator->validate($gcm, [
            new NotBlank()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $deviceManager = $this->modelManagerFactory->getModelManager('device');
        $deviceClass = $deviceManager->getClassName();
        $device = new $deviceClass();

        $device->setDeviceId($deviceId)
            ->setGcm($gcm);

        $deviceManager->updateModel($device, array(
            'gcm' => $oldGcm
        ));

        return $device;
    }

}