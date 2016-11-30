<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 14.56
 */

namespace App\MateyModels;



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

    public function setValuesFromArray($values)
    {
        $this->deviceId = isset($values['device_id']) ? $values['device_id'] : "";
        $this->userId = isset($values['user_id']) ? $values['user_id'] : "";
        $this->dateTime = isset($values['date_time']) ? $values['date_time'] : "";
        $this->status = isset($values['status']) ? $values['status'] : "";
        $this->gcm = isset($values['gcm']) ? $values['gcm'] : "";
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->deviceId) ? : $keyValues['device_id'] = $this->deviceId;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->dateTime) ? : $keyValues['date_time'] = $this->dateTime;
        empty($this->status) ? : $keyValues['status'] = $this->status;
        empty($this->gcm) ? : $keyValues['gcm'] = $this->gcm;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = array ();

        empty($this->deviceId) ? : $keyValues['device_id'] = $this->deviceId;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->dateTime) ? : $keyValues['date_time'] = $this->dateTime;
        empty($this->status) ? : $keyValues['status'] = $this->status;
        empty($this->gcm) ? : $keyValues['gcm'] = $this->gcm;

        return $keyValues;
    }


}