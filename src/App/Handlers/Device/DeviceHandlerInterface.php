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

    public function handleCreateDevice(Application $app, Request $request);

    public function handleUpdateDevice(Application $app, Request $request, $deviceId);

    public function handleLogin (Application $app, Request $request, $deviceId);

}