<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 17.42
 */

namespace App\MateyModels;


class Newsfeed
{

    protected $userId;
    protected $feedName;
    protected $dateTime;
    protected $feedActivities = array();
    protected $feedGroups = array();

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
    public function getFeedName()
    {
        return $this->feedName;
    }

    /**
     * @param mixed $feedName
     */
    public function setFeedName($feedName)
    {
        $this->feedName = $feedName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFeedActivities()
    {
        return $this->feedActivities;
    }

    /**
     * @param mixed $feedActivities
     */
    public function setFeedActivities(array $feedActivities)
    {
        $this->feedActivities = $feedActivities;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFeedGroups()
    {
        return $this->feedGroups;
    }

    /**
     * @param mixed $feedGroups
     */
    public function setFeedGroups(array $feedGroups)
    {
        $this->feedGroups = $feedGroups;
        return $this;
    }

}