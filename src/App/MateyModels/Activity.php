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

    const USER_TYPE = "MATEY_USER";
    const POST_TYPE = "POST";
    const GROUP_TYPE = "GROUP";
    const REPLY_TYPE = "REPLY";
    const REREPLY_TYPE = "REREPLY";

    const BOOKMARK_ACT = "BOOKMARK";
    const SHARE_ACT = "SHARE";
    const BOOST_ACT = "BOOST";
    const APPROVE_ACT = "APPROVE";
    const FOLLOW_ACT = "FOLLOW";
    const ARCHIVE_ACT = "ARCHIVE";
    const REPLY_CREATE_ACT = "REPLY_CREATE";
    const REREPLY_CREATE_ACT = "REREPLY_CREATE";
    const GROUP_CREATE_ACT = "GROUP_CREATE";
    const POST_CREATE_ACT ="POST_CREATE";

    protected $activityId;
    protected $userId;
    protected $sourceId;
    protected $sourceType;
    protected $parentId;
    protected $parentType;
    protected $activityType;
    protected $timeC;

    public function setId($id) {
        return $this->setActivityId($id);
    }

    /**
     * @return mixed
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @param mixed $activityId
     */
    public function setActivityId($activityId)
    {
        if($activityId !== null) $activityId = (int) $activityId;
        $this->activityId = $activityId;
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
        if($userId !== null) $userId = (int) $userId;
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
        if($sourceId !== null) $sourceId = (int) $sourceId;
        $this->sourceId = $sourceId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @param mixed $sourceType
     */
    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;
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
        if($parentId !== null) $parentId = (int) $parentId;
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
        $this->timeC = $timeC;
        return $this;
    }

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'activity_id') {
            if($type == 'get') return $this->getActivityId();
            else return $this->setActivityId($props['value']);
        }
        else if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'source_id') {
            if($type == 'get') return $this->getSourceId();
            else return $this->setSourceId($props['value']);
        }
        else if($props['key'] == 'source_type') {
            if($type == 'get') return $this->getSourceType();
            else return $this->setSourceType($props['value']);
        }
        else if($props['key'] == 'parent_id') {
            if($type == 'get') return $this->getParentId();
            else return $this->setParentId($props['value']);
        }
        else if($props['key'] == 'parent_type') {
            if($type == 'get') return $this->getParentType();
            else return $this->setParentType($props['value']);
        }
        else if($props['key'] == 'activity_type') {
            if($type == 'get') return $this->getActivityType();
            else return $this->setActivityType($props['value']);
        }
        else if($props['key'] == 'time_c') {
            if($type == 'get') return $this->getTimeC()->format(DefaultDates::DATE_FORMAT);
            else return $this->setTimeC($this->createDateTimeFromString($props['value']));
        }
    }


}