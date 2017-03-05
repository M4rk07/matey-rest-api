<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.01
 */

namespace App\MateyModels;


class GroupFavorite extends AbstractModel
{

    protected $userId;
    protected $groupId;
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
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $postId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
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
        $this->groupId = isset($values['group_id']) ? $values['group_id'] : "";
        $this->timeC = isset($values['time_c']) ? $values['time_c'] : "";
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->groupId) ? : $keyValues['group_id'] = $this->groupId;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->timeC;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        return $keyValues;
    }

}