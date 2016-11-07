<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 02.11
 */

namespace App\MateyModels;


use App\OAuth2Models\AbstractModel;
use AuthBucket\OAuth2\Model\ModelInterface;

class Activity extends AbstractModel
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

    public function setValuesFromArray($values)
    {
        $this->activityId = isset($values['activity_id']) ? $values['activity_id'] : "";
        $this->userId = isset($values['user_id']) ? $values['user_id'] : "";
        $this->sourceId = isset($values['source_id']) ? $values['source_id'] : "";
        $this->parentId = isset($values['parent_id']) ? $values['parent_id'] : "";
        $this->parentType = isset($values['parent_type']) ? $values['parent_type'] : "";
        $this->activityType = isset($values['activity_type']) ? $values['activity_type'] : "";
        $this->activityTime = isset($values['activity_time']) ? $values['activity_time'] : "";
        $this->srlData = isset($values['srlData']) ? $values['srlData'] : "";
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array ();

        empty($this->activityId) ? : $keyValues['activity_id'] = $this->activityId;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->sourceId) ? : $keyValues['source_id'] = $this->sourceId;
        empty($this->parentId) ? : $keyValues['parent_id'] = $this->parentId;
        empty($this->parentType) ? : $keyValues['parent_type'] =$this->parentType;
        empty($this->activityType) ? : $keyValues['activity_type'] = $this->activityType;
        empty($this->activityTime) ? : $keyValues['activity_time'] = $this->activityTime;
        empty($this->srlData) ? : $keyValues['srlData'] = $this->srlData;

        return $keyValues;
    }


}