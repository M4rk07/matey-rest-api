<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 01.14
 */

namespace App\MateyModels;


use App\OAuth2Models\AbstractModel;
use AuthBucket\OAuth2\Model\ModelInterface;

class Device extends AbstractModel
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

    public function setValuesFromArray($values)
    {
        $this->deviceId = isset($values['device_id']) ? $values['device_id'] : "";
        $this->deviceSecret = isset($values['device_secret']) ? $values['device_secret'] : "";
        $this->gcm = isset($values['gcm']) ? $values['gcm'] : "";
        $this->dateRegistered = isset($values['date_registered']) ? $values['date_registered'] : "";
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array ();

        empty($this->deviceId) ? : $keyValues['device_id'] = $this->deviceId;
        empty($this->deviceSecret) ? : $keyValues['device_secret'] = $this->deviceSecret;
        empty($this->gcm) ? : $keyValues['gcm'] = $this->gcm;
        empty($this->dateRegistered) ? : $keyValues['date_registered'] = $this->dateRegistered;

        return $keyValues;
    }


}