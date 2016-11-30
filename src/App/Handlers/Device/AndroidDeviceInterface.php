<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 10.11
 */

namespace App\Handlers\Device;


interface AndroidDeviceInterface
{

    public function getGcmById($deviceId);

    public function updateGcmById($deviceId, $gcm, $oldGcm);

}