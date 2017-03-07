<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 14.58
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;

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
        return $this;
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
        return $this;
    }

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'parent_id') {
            if($type == 'get') return $this->getParentId();
            else return $this->setParentId($props['value']);
        }
        else if($props['key'] == 'parent_type') {
            if($type == 'get') return $this->getParentType();
            else return $this->setParentType($props['value']);
        }
        else if($props['key'] == 'time_c') {
            if($type == 'get') return $this->getTimeC()->format(DefaultDates::DATE_FORMAT);
            else return $this->setTimeC($this->createDateTimeFromString($props['value']));
        }
        else if($props['key'] == 'num_of_interactions') {
            if($type == 'get') return $this->getNumOfInteractions();
            else return $this->setNumOfInteractions($props['value']);
        }
        else if($props['key'] == 'sum_of_interactions') {
            if($type == 'get') return $this->getSumOfInteractions();
            else return $this->setSumOfInteractions($props['value']);
        }
    }


}