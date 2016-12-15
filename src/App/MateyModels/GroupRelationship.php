<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.12.16.
 * Time: 16.30
 */

namespace App\MateyModels;


class GroupRelationship extends AbstractModel
{

    protected $groupId;
    protected $userId;
    protected $role;
    protected $dateCreated;

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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function setValuesFromArray($values)
    {
        if(isset($values['group_id'])) $this->setId($values['group_id']);
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['role'])) $this->setRole($values['role']);
        if(isset($values['date_created'])) $this->setDateCreated($values['date_created']);
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['group_id'] = $this->id;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->role) ? : $keyValues['role'] = $this->role;
        empty($this->dateCreated) ? : $keyValues['date_created'] = $this->dateCreated;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();
        return $keyValues;
    }


}