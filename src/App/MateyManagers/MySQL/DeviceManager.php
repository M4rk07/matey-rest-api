<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 01.15
 */

namespace App\MateyModels;


use App\MateyModels\Device;
use App\Services\BaseService;

class DeviceManager extends AbstractManager
{
    public function __construct ($db) {
        parent::__construct($db);
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Device';
    }

    public function getTableName() {
        return self::T_DEVICE;
    }

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

    public function getDeviceWithGcmById($device_id){

        $result = $this->db->fetchAll("SELECT gcm FROM ".self::T_DEVICE." WHERE device_id = ? LIMIT 1",
            array($device_id));

        $device = new Device();
        $device->setDeviceId($device_id)
            ->setGcm($result[0]['gcm']);

        return $device;

    }

}