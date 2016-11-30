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
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        DeviceHandlerFactoryInterface $deviceHandlerFactory
    ) {
        parent::__construct($validator, $modelManagerFactory);
        $this->deviceHandlerFactory = $deviceHandlerFactory;
    }

    public function createDeviceAction (Request $request, $deviceType) {

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->createDevice($request);

    }

    public function updateDeviceAction (Request $request, $deviceType) {

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->updateDevice($request);

    }

    public function loginOnDeviceAction (Application $app, Request $request, $deviceType) {

        return $this->deviceHandlerFactory
            ->getDeviceHandler($deviceType)
            ->loginOnDevice($app, $request);

    }

}