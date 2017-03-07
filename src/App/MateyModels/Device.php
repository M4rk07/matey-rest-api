<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 01.14
 */

namespace App\MateyModels;

use App\Constants\Defaults\DefaultDates;
use AuthBucket\OAuth2\Model\ModelInterface;

class Device extends AbstractModel
{

    protected $deviceId;
    protected $deviceSecret;
    protected $gcm;
    protected $dateRegistered;

    public function setId($id) {
        return $this->setDeviceId($id);
    }

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
        return $this;
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
        return $this;
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
        return $this;
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
        return $this;
    }

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'device_id') {
            if($type == 'get') return $this->getDeviceId();
            else return $this->setDeviceId($props['value']);
        }
        else if($props['key'] == 'device_secret') {
            if($type == 'get') return $this->getDeviceSecret();
            else return $this->setDeviceSecret($props['value']);
        }
        else if($props['key'] == 'gcm') {
            if($type == 'get') return $this->getGcm();
            else return $this->setGcm($props['value']);
        }
        else if($props['key'] == 'date_registered') {
            if($type == 'get') return $this->getDateRegistered()->format(DefaultDates::DATE_FORMAT);
            else return $this->setDateRegistered($this->createDateTimeFromString($props['value']));
        }
    }


}