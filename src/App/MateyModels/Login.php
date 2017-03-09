<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 14.56
 */

namespace App\MateyModels;



use App\Constants\Defaults\DefaultDates;
use AuthBucket\OAuth2\Model\ModelInterface;

class Login extends AbstractModel
{

    protected $deviceId;
    protected $userId;
    protected $dateTime;
    protected $status;
    protected $gcm;

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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'device_id') {
            if($type == 'get') return $this->getDeviceId();
            else return $this->setDeviceId($props['value']);
        }
        else if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'date_time') {
            if($type == 'get') return $this->getDateTime();
            else return $this->setDateTime($this->createDateTimeFromString($props['value']));
        }
        else if($props['key'] == 'status') {
            if($type == 'get') return $this->getStatus();
            else return $this->setStatus($props['value']);
        }
        else if($props['key'] == 'gcm') {
            if($type == 'get') return $this->getGcm();
            else return $this->setGcm($props['value']);
        }
    }


}