<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.12.16.
 * Time: 16.30
 */

namespace App\MateyModels;


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

    public function setValuesFromArray($values)
    {
        if(isset($values['group_id'])) $this->setId($values['group_id']);
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['scope'])) $this->setScope($values['scope']);
        if(isset($values['time_c'])) $this->setTimeC($values['time_c']);
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['group_id'] = $this->id;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->scope) ? : $keyValues['scope'] = $this->scope;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->timeC;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();
        return $keyValues;
    }


}