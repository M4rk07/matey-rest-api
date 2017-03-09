<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.12.16.
 * Time: 16.30
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;

class GroupAdmin extends AbstractModel
{

    protected $groupId;
    protected $userId;
    protected $scope;
    protected $timeC;

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
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
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param mixed $role
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
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
     * @param mixed $dateCreated
     */
    public function setTimeC($timeC)
    {
        $this->timeC = $timeC;
        return $this;
    }

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'group_id') {
            if($type == 'get') return $this->getGroupId();
            else return $this->setGroupId($props['value']);
        }
        else if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'scope') {
            if($type == 'get') return $this->getScope();
            else return $this->setScope($props['value']);
        }
        else if($props['key'] == 'time_c') {
            if($type == 'get') return $this->getTimeC();
            else return $this->setTimeC($this->createDateTimeFromString($props['value']));
        }
    }

}