<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 12.55
 */

namespace App\MateyModels;


use AuthBucket\OAuth2\Model\ModelInterface;

class Post extends AbstractModel
{

    protected $groupId;
    protected $userId;
    protected $title;
    protected $text;
    protected $timeC;
    protected $attachsNum;
    protected $locationsNum;

    protected $lastActions;
    protected $numOfReplies;
    protected $numOfShares;
    protected $numOfBoosts;
    protected $timestamp;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
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

    /**
     * @return mixed
     */
    public function getAttachsNum()
    {
        return $this->attachsNum;
    }

    /**
     * @param mixed $attachsNum
     */
    public function setAttachsNum($attachsNum)
    {
        $this->attachsNum = $attachsNum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationsNum()
    {
        return $this->locationsNum;
    }

    /**
     * @param mixed $locationsNum
     */
    public function setLocationsNum($locationsNum)
    {
        $this->locationsNum = $locationsNum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastActions()
    {
        return $this->lastActions;
    }

    /**
     * @param mixed $lastActions
     */
    public function setLastActions($lastActions)
    {
        $this->lastActions = $lastActions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfReplies()
    {
        return $this->numOfReplies;
    }

    /**
     * @param mixed $numOfReplies
     */
    public function setNumOfReplies($numOfReplies)
    {
        $this->numOfReplies = $numOfReplies;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfShares()
    {
        return $this->numOfShares;
    }

    /**
     * @param mixed $numOfShares
     */
    public function setNumOfShares($numOfShares)
    {
        $this->numOfShares = $numOfShares;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfBoosts()
    {
        return $this->numOfBoosts;
    }

    /**
     * @param mixed $numOfBoosts
     */
    public function setNumOfBoosts($numOfBoosts)
    {
        $this->numOfBoosts = $numOfBoosts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }



    public function serialize() {
        return serialize(array(
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'text' => $this->text
        ));
    }

    public function unserialize($data) {
        $data = unserialize($data);
        $this->setId($data['post_id'])
            ->setUserId($data['user_id'])
            ->setText($data['text']);
        return $this;
    }

    public function setValuesFromArray($values)
    {
        if(isset($values['post_id'])) $this->setId($values['post_id']);
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['group_id'])) $this->setGroupId($values['group_id']);
        if(isset($values['title'])) $this->setTitle($values['title']);
        if(isset($values['text'])) $this->setText($values['text']);
        if(isset($values['time_c'])) $this->setTimeC($values['time_c']);
        if(isset($values['attachs_num'])) $this->setAttachsNum($values['attachs_num']);
        if(isset($values['locations_num'])) $this->setLocationsNum($values['locations_num']);
        if(isset($values['num_of_shares'])) $this->setNumOfShares($values['num_of_shares']);
        if(isset($values['num_of_replies'])) $this->setNumOfReplies($values['num_of_replies']);
        if(isset($values['num_of_boosts'])) $this->setNumOfBoosts($values['num_of_boosts']);
        if(isset($values['last_action'])) $this->setNumOfBoosts($values['last_action']);
        if(isset($values['timestamp'])) $this->setTimestamp($values['timestamp']);
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['post_id'] = $this->id;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->groupId) ? : $keyValues['group_id'] = $this->groupId;
        empty($this->title) ? : $keyValues['title'] = $this->title;
        empty($this->text) ? : $keyValues['text'] = $this->text;
        empty($this->attachsNum) ? : $keyValues['attachs_num'] = $this->attachsNum;
        empty($this->locationsNum) ? : $keyValues['locations_num'] = $this->locationsNum;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->timeC;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        empty($this->numOfShares) ? : $keyValues['num_of_shares'] = $this->numOfShares;
        empty($this->numOfReplies) ? : $keyValues['num_of_replies'] = $this->numOfReplies;
        empty($this->numOfBoosts) ? : $keyValues['num_of_boosts'] = $this->numOfBoosts;
        empty($this->lastActions) ? : $keyValues['last_action'] = $this->lastActions;
        empty($this->timestamp) ? : $keyValues['timestamp'] = $this->timestamp;

        return $keyValues;
    }


}