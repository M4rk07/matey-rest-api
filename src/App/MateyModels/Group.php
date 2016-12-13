<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.52
 */

namespace App\MateyModels;


class Group extends AbstractModel
{

    protected $userId;
    protected $groupName;
    protected $description;
    protected $privacy;
    protected $dateCreated;
    protected $numOfFollowers;

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
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param mixed $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * @param mixed $privacy
     */
    public function setPrivacy($privacy)
    {
        $this->privacy = $privacy;
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

    /**
     * @return mixed
     */
    public function getNumOfFollowers()
    {
        return $this->numOfFollowers;
    }

    /**
     * @param mixed $numOfFollowers
     */
    public function setNumOfFollowers($numOfFollowers)
    {
        $this->numOfFollowers = $numOfFollowers;
        return $this;
    }


    public function setValuesFromArray($values)
    {
        if(isset($values['group_id'])) $this->setId($values['group_id']);
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['group_name'])) $this->setGroupName($values['group_name']);
        if(isset($values['description'])) $this->setDescription($values['description']);
        if(isset($values['date_created'])) $this->setDateCreated($values['date_created']);
        if(isset($values['num_of_followers'])) $this->setNumOfFollowers($values['num_of_followers']);
    }

    public function getMysqlValues()
    {
        empty($this->id) ? : $keyValues['group_id'] = $this->id;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->groupName) ? : $keyValues['group_name'] = $this->groupName;
        empty($this->description) ? : $keyValues['description'] = $this->description;
        empty($this->dateCreated) ? : $keyValues['date_created'] = $this->dateCreated;
        empty($this->numOfFollowers) ? : $keyValues['num_of_followers'] = $this->numOfFollowers;
    }

    public function getValuesAsArray()
    {
        // TODO: Implement getValuesAsArray() method.
    }


}