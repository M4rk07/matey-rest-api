<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 02.11
 */

namespace App\MateyModels;


class Activity extends MateyModel
{

    protected $activityId;
    protected $userId;
    protected $sourceId;
    protected $parentId;
    protected $parentType;
    protected $activityType;
    protected $activityTime;
    protected $srlData;

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
    public function getActivityTime()
    {
        return $this->activityTime;
    }

    /**
     * @param mixed $activityTime
     */
    public function setActivityTime($activityTime)
    {
        $this->activityTime = $activityTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSrlData()
    {
        return $this->srlData;
    }

    /**
     * @param mixed $srlData
     */
    public function setSrlData($srlData)
    {
        $this->srlData = $srlData;
        return $this;
    }




}