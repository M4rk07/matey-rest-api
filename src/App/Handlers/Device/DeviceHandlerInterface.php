<?php

namespace App\Handlers\Device;
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

    public function updateDevice(Request $request);

    public function loginOnDevice(Request $request);

    public function logoutOfDevice(Request $request);

}