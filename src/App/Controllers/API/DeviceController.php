<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 10.32
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Device\DeviceHandlerFactoryInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeviceController extends AbstractController
{

    protected $deviceHandlerFactory;

    public function __construct(
        DeviceHandlerFactoryInterface $deviceHandlerFactory
    ) {
        $this->deviceHandlerFactory = $deviceHandlerFactory;
    }

    public function createDeviceAction (Application $app, Request $request) {

        $deviceType = $request->request->get('type');
        $deviceType = !empty($deviceType) ? : "android";

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->handleCreateDevice($app, $request);

    }

    public function updateDeviceAction (Application $app, Request $request, $deviceId) {

        $deviceType = $request->request->get('type');
        $deviceType = !empty($deviceType) ? : "android";

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->handleUpdateDevice($app, $request, $deviceId);

    }

    public function loginOnDeviceAction (Application $app, Request $request, $deviceId) {

        $deviceType = $request->request->get('type');
        $deviceType = !empty($deviceType) ? : "android";

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->handleLogin ($app, $request, $deviceId);

    }

}