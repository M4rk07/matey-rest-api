<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 01.15
 */

namespace App\MateyManagers;


use App\MateyModels\Device;
use App\Services\BaseService;

class DeviceManager extends BaseService
{

    public function createDevice(Device $device) {
        $this->db->executeUpdate("INSERT INTO ".self::T_DEVICE." (gcm, device_secret) VALUES (?,?)",
            array($device->getGcm(), $device->getDeviceSecret()));

        $device->setDeviceId($this->db->lastInsertId());
        return $device;
    }

    public function updateDevice(Device $device, $oldGcm) {

        $result = $this->db->executeUpdate("UPDATE ".self::T_DEVICE." SET gcm = ? WHERE device_id = ? AND gcm = ?",
            array($device->getGcm(), $device->getDeviceId(), $oldGcm));

        if($result>0) return true;
        return false;
    }

}