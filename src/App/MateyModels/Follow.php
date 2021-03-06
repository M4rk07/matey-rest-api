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
    protected $lastInteraction;

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
        $this->userId = (int)$userId;
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
        $this->parentId = (int)$parentId;
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

    /**
     * @return mixed
     */
    public function getLastInteraction()
    {
        return $this->lastInteraction;
    }

    /**
     * @param mixed $lastInteraction
     */
    public function setLastInteraction($lastInteraction)
    {
        $this->lastInteraction = $lastInteraction;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        $timeFollowed = $this->getTimeC();
        $lastInteractions = $this->getLastInteraction();
        $sumOfInteractions = $this->getSumOfInteractions();
        $numOfInteractions = $this->getNumOfInteractions();

        $now = new \DateTime(DefaultDates::DATE_FORMAT);

        $daysSinceFollowed = $now->diff($timeFollowed)->format("%a");
        $daysSinceLastInteraction = $now->diff($lastInteractions)->format("%a");

        return ($sumOfInteractions*0.2)+(($numOfInteractions/$daysSinceFollowed)*0.5)+((1/$daysSinceLastInteraction)*0.3);
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
            if($type == 'get') return $this->getTimeC();
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
        else if ($props['key'] == 'last_interaction') {
            if ($type == 'get') return $this->getLastInteraction();
            else return $this->setLastInteraction($props['value']);
        }
    }


}