<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 18.59
 */

namespace App\MateyModels;

use App\Constants\Defaults\DefaultDates;
use AuthBucket\OAuth2\Model\ModelInterface;

class Approve extends AbstractModel
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
    }

}