<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 02.11
 */

namespace App\MateyModels;

use App\Constants\Defaults\DefaultDates;

class Activity extends AbstractModel
{

    const USER_TYPE = "USER";
    const POST_TYPE = "POST";
    const GROUP_TYPE = "GROUP";
    const SHARE_TYPE = "SHARE";
    const BOOKMARK_TYPE = "BOOKMARK";
    const BOOST_TYPE = "BOOST";
    const REPLY_TYPE = "REPLY";
    const REREPLY_TYPE = "REREPLY";
    const APPROVE_TYPE = "APPROVE";
    const FOLLOW_TYPE = "FOLLOW";

    protected $userId;
    protected $sourceId;
    protected $parentId;
    protected $parentType;
    protected $activityType;
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
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @param mixed $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
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
    public function getActivityType()
    {
        return $this->activityType;
    }

    /**
     * @param mixed $activityType
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
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
     * @param mixed $activityTime
     */
    public function setTimeC($timeC)
    {
        $this->timeC = $this->createDateTimeFromString($timeC);
        return $this;
    }

    public function setValuesFromArray($values)
    {

        if(isset($values['activity_id'])) $this->setId($values['activity_id']);
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['source_id'])) $this->setSourceId($values['source_id']);
        if(isset($values['parent_id'])) $this->setParentId($values['parent_id']);
        if(isset($values['parent_type'])) $this->setParentType($values['parent_type']);
        if(isset($values['activity_type'])) $this->setActivityType($values['activity_type']);
        if(isset($values['time_c'])) $this->setTimeC($values['time_c']);
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['activity_id'] = $this->id;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->sourceId) ? : $keyValues['source_id'] = $this->sourceId;
        empty($this->parentId) ? : $keyValues['parent_id'] = $this->parentId;
        empty($this->parentType) ? : $keyValues['parent_type'] =$this->parentType;
        empty($this->activityType) ? : $keyValues['activity_type'] = $this->activityType;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->getTimeC()->format(DefaultDates::DATE_FORMAT);

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        return $keyValues;
    }


}