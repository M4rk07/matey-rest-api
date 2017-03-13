<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 10.14
 */

namespace App\Handlers\Device;


use App\Constants\Defaults\DefaultDates;
use App\Exception\NotFoundException;
use App\MateyModels\Login;
use App\Security\SecretGenerator;
use App\Validators\DeviceId;
use App\Validators\GCM;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints\NotBlank;

class AndroidDeviceHandler extends AbstractDeviceHandler implements AndroidDeviceInterface
{
    public function handleCreateDevice(Application $app, Request $request)
    {
        $gcm = $request->request->get("gcm");
        $this->validateValue($gcm, array(
            new NotBlank(),
            new GCM()
        ));

        $secretGenerator = new SecretGenerator();
        $deviceSecret = $secretGenerator->generateDeviceSecret();

        $deviceManager = $this->modelManagerFactory->getModelManager('device');
        $device = $deviceManager->getModel();

        $device->setDeviceSecret($deviceSecret)
            ->setGcm($gcm);

        /*
         * Creating new device
         */
        $device = $deviceManager->createModel($device);

        return new JsonResponse($device->asArray(), 200);
    }

    public function handleUpdateDevice (Application $app, Request $request, $deviceId)
    {
        $oldGcm = $request->request->get("old_gcm");
        $gcm = $request->request->get("gcm");

        $this->validateValue($deviceId, array(
            new NotBlank(),
            new DeviceId()
        ));
        $this->validateValue($oldGcm, array(
            new NotBlank(),
            new GCM()
        ));
        $this->validateValue($gcm, array(
            new NotBlank(),
            new GCM()
        ));

        $device = $this->updateGcmById($deviceId, $gcm, $oldGcm);

        return new JsonResponse($device->asArray(), 200);
    }

    public function handleLogin (Application $app, Request $request, $deviceId)
    {
        $userId = $request->request->get("user_id");

        $device = $this->getGcmById($deviceId);

        if(empty($device)) throw new ResourceNotFoundException();

        $loginManager = $this->modelManagerFactory->getModelManager('login');
        $login = $loginManager->getModel();

        if($request->getMethod() == "PUT") {
            /*
            $loginCheck = $loginManager->readModelOneBy(array(
                'device_id' => $deviceId,
                'user_id' => $userId,
                'status' => 1
            ), null, array('user_id'));

            if(!empty($loginCheck)) throw new InvalidRequestException(array(
                'description' => "Hey, you are already logged in."
            ));
            */

            $login->setUserId($userId)
                ->setDeviceId($deviceId)
                ->setGcm($device->getGcm());
            $loginManager->createModel($login);

        }

        else if($request->getMethod() == "DELETE") {

            $login->setStatus(0);
            $loginManager->updateModel($login, array(
                'device_id' => $deviceId,
                'user_id' => $userId
            ));

        }

        return $app['matey.user_controller']->getUserAction($app, $request, $userId);
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

        $deviceManager = $this->modelManagerFactory->getModelManager('device');
        $deviceClass = $deviceManager->getClassName();
        $device = new $deviceClass();

        $device->setDeviceId($deviceId)
            ->setGcm($gcm);

        $result = $deviceManager->updateModel($device, array(
            'gcm' => $oldGcm
        ));

        if($result <= 0) throw new NotFoundException();

        return $device;
    }

}