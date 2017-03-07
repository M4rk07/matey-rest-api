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

    public function createDeviceAction (Request $request) {

        $deviceType = $request->request->get('type');
        $deviceType = !empty($deviceType) ? : "android";

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->createDevice($request);

    }

    public function updateDeviceAction (Request $request, $deviceId) {

        $deviceType = $request->request->get('type');
        $deviceType = !empty($deviceType) ? : "android";

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->updateDevice($request, $deviceId);

    }

    public function loginOnDeviceAction (Application $app, Request $request, $deviceId) {

        $deviceType = $request->request->get('type');
        $deviceType = !empty($deviceType) ? : "android";

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->loginOnDevice($app, $request, $deviceId);

    }

}