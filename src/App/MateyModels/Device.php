<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 01.14
 */

namespace App\MateyModels;


class Device
{

    protected $deviceId;
    protected $deviceSecret;
    protected $gcm;
    protected $dateRegistered;

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param mixed $deviceId
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @return mixed
     */
    public function getDeviceSecret()
    {
        return $this->deviceSecret;
    }

    /**
     * @param mixed $deviceSecret
     */
    public function setDeviceSecret($deviceSecret)
    {
        $this->deviceSecret = $deviceSecret;
    }

    /**
     * @return mixed
     */
    public function getGcm()
    {
        return $this->gcm;
    }

    /**
     * @param mixed $gcm
     */
    public function setGcm($gcm)
    {
        $this->gcm = $gcm;
    }

    /**
     * @return mixed
     */
    public function getDateRegistered()
    {
        return $this->dateRegistered;
    }

    /**
     * @param mixed $dateRegistered
     */
    public function setDateRegistered($dateRegistered)
    {
        $this->dateRegistered = $dateRegistered;
    }

}