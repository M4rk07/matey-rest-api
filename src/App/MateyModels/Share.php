<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.10
 */

namespace App\MateyModels;


class Share extends AbstractModel
{

    protected $userId;
    protected $parentId;
    protected $parentType;
    protected $timeC;

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
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentType()
    {
        return $this->parentType;
    }

    /**
     * @param mixed $parentType
     */
    public function setParentType($parentType)
    {
        $this->parentType = $parentType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeC()
    {
        return $this->timeC;
    }

    /**
     * @param mixed $timeC
     */
    public function setTimeC($timeC)
    {
        $this->timeC = $timeC;
        return $this;
    }

    public function setValuesFromArray($values)
    {
        $this->userId = isset($values['user_id']) ? $values['user_id'] : "";
        $this->parentId = isset($values['parent_id']) ? $values['parent_id'] : "";
        $this->parentType = isset($values['parent_type']) ? $values['parent_type'] : "";
        $this->timeC = isset($values['time_c']) ? $values['time_c'] : "";
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->parentId) ? : $keyValues['parent_id'] = $this->parentId;
        empty($this->parentType) ? : $keyValues['parent_type'] = $this->parentType;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->timeC;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        return $keyValues;
    }

}