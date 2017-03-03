<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 14.58
 */

namespace App\MateyModels;


class Follow extends AbstractModel
{

    protected $userId;
    protected $parentId;
    protected $parentType;
    protected $timeC;
    protected $numOfInteractions;
    protected $sumOfInteractions;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userFrom
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

    /**
     * @return mixed
     */
    public function getNumOfInteractions()
    {
        return $this->numOfInteractions;
    }

    /**
     * @param mixed $numOfInteractions
     */
    public function setNumOfInteractions($numOfInteractions)
    {
        $this->numOfInteractions = $numOfInteractions;
    }

    /**
     * @return mixed
     */
    public function getSumOfInteractions()
    {
        return $this->sumOfInteractions;
    }

    /**
     * @param mixed $sumOfInteractions
     */
    public function setSumOfInteractions($sumOfInteractions)
    {
        $this->sumOfInteractions = $sumOfInteractions;
    }

    public function setValuesFromArray($values)
    {
        $this->userId = isset($values['user_id']) ? $values['user_id'] : "";
        $this->parentId = isset($values['parent_id']) ? $values['parent_id'] : "";
        $this->parentType = isset($values['parent_type']) ? $values['parent_type'] : "";
        $this->timeC = isset($values['time_c']) ? $values['time_c'] : "";
        $this->numOfInteractions = isset($values['num_of_interactions']) ? $values['num_of_interactions'] : "";
        $this->sumOfInteractions = isset($values['sum_of_interactions']) ? $values['sum_of_interactions'] : "";
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

        empty($this->numOfInteractions) ? : $keyValues['num_of_interactions'] = $this->numOfInteractions;
        empty($this->sumOfInteractions) ? : $keyValues['sum_of_interactions'] = $this->sumOfInteractions;

        return $keyValues;
    }


}