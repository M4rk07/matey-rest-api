<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.01
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;

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

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'group_id') {
            if($type == 'get') return $this->getGroupId();
            else return $this->setGroupId($props['value']);
        }
        else if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'time_c') {
            if($type == 'get') return $this->getTimeC()->format(DefaultDates::DATE_FORMAT);
            else return $this->setTimeC($this->createDateTimeFromString($props['value']));
        }
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