<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 10.28
 */

namespace App\Handlers\Device;


interface DeviceHandlerFactoryInterface
{

    /**
     * @param string $type type if registration handler
     * @return DeviceHandlerInterface
     */
    public function getDeviceHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getDeviceHandlers();

}