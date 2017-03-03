<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.13
 */

namespace App\MateyModels;

use AuthBucket\OAuth2\Model\ModelInterface;

class Reply extends AbstractModel
{
    protected $postId;
    protected $userId;
    protected $text;
    protected $attachsNum;
    protected $locationsNum;
    protected $timeC;
    protected $numOfApproves;
    protected $numOfReplies;

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
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
    public function getNumOfApproves()
    {
        return $this->numOfApproves;
    }

    /**
     * @param mixed $numOfApproves
     */
    public function setNumOfApproves($numOfApproves)
    {
        $this->numOfApproves = $numOfApproves;
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



    public function serialize() {
        return serialize(array(
            'response_id' => $this->responseId,
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'text' => $this->text,
            'date_time' => $this->dateTime
        ));
    }

    public function setValuesFromArray($values)
    {
        if(isset($values['post_id'])) $this->setId($values['post_id']);
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['post_id'])) $this->setPostId($values['post_id']);
        if(isset($values['text'])) $this->setText($values['text']);
        if(isset($values['time_c'])) $this->setTimeC($values['time_c']);
        if(isset($values['attachs_num'])) $this->setAttachsNum($values['attachs_num']);
        if(isset($values['locations_num'])) $this->setLocationsNum($values['locations_num']);
        if(isset($values['num_of_replies'])) $this->setNumOfReplies($values['num_of_replies']);
        if(isset($values['num_of_approves'])) $this->setNumOfApproves($values['num_of_approves']);
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['reply_id'] = $this->id;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->postId) ? : $keyValues['post_id'] = $this->postId;
        empty($this->text) ? : $keyValues['text'] = $this->text;
        empty($this->attachsNum) ? : $keyValues['attachs_num'] = $this->attachsNum;
        empty($this->locationsNum) ? : $keyValues['locations_num'] = $this->locationsNum;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->timeC;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        empty($this->numOfReplies) ? : $keyValues['num_of_replies'] = $this->numOfReplies;
        empty($this->numOfApproves) ? : $keyValues['num_of_approves'] = $this->numOfApproves;

        return $keyValues;
    }


}