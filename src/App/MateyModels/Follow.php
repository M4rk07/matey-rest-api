<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 14.58
 */

namespace App\MateyModels;


use AuthBucket\OAuth2\Model\ModelInterface;

class Follow extends AbstractModel
{

    protected $userFrom;
    protected $userTo;
    protected $dateTime;

    /**
     * @return mixed
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * @param mixed $userFrom
     */
    public function setUserFrom($userFrom)
    {
        $this->userFrom = $userFrom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserTo()
    {
        return $this->userTo;
    }

    /**
     * @param mixed $userTo
     */
    public function setUserTo($userTo)
    {
        $this->userTo = $userTo;
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

    public function setValuesFromArray($values)
    {
        $this->userFrom = isset($values['from_user']) ? $values['from_user'] : "";
        $this->userTo = isset($values['to_user']) ? $values['to_user'] : "";
        $this->dateTime = isset($values['date_time']) ? $values['date_time'] : "";
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->userFrom) ? : $keyValues['from_user'] = $this->userFrom;
        empty($this->userTo) ? : $keyValues['to_user'] = $this->userTo;
        empty($this->dateTime) ? : $keyValues['date_time'] = $this->dateTime;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = array ();

        empty($this->userFrom) ? : $keyValues['from_user'] = $this->userFrom;
        empty($this->userTo) ? : $keyValues['to_user'] = $this->userTo;
        empty($this->dateTime) ? : $keyValues['date_time'] = $this->dateTime;

        return $keyValues;
    }


}