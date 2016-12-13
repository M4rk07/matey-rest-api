<?php

namespace App\Handlers\Device;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 10.07
 */
interface DeviceHandlerInterface
{

    public function createDevice(Request $request);

    public function updateDevice(Request $request, $deviceId);

    public function loginOnDevice(Application $app, Request $request, $deviceId);

}