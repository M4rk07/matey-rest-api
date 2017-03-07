<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.13
 */

namespace App\MateyModels;

use App\Constants\Defaults\DefaultDates;
use AuthBucket\OAuth2\Model\ModelInterface;

class Reply extends AbstractModel
{
    protected $replyId;
    protected $postId;
    protected $userId;
    protected $text;
    protected $attachsNum;
    protected $locationsNum;
    protected $timeC;
    protected $numOfApproves;
    protected $numOfReplies;

    public function setId($id) {
        return $this->setReplyId($id);
    }

    /**
     * @return mixed
     */
    public function getReplyId()
    {
        return $this->replyId;
    }

    /**
     * @param mixed $replyId
     */
    public function setReplyId($replyId)
    {
        $this->replyId = $replyId;
        return $this;
    }

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

    public function getSetFunction (array $props, $type = 'get')
    {
        if ($props['key'] == 'reply_id') {
            if ($type == 'get') return $this->getReplyId();
            else return $this->setReplyId($props['value']);
        }
        if ($props['key'] == 'post_id') {
            if ($type == 'get') return $this->getPostId();
            else return $this->setPostId($props['value']);
        }
        else if ($props['key'] == 'user_id') {
            if ($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if ($props['key'] == 'text') {
            if ($type == 'get') return $this->getText();
            else return $this->setText($props['value']);
        }
        else if($props['key'] == 'time_c') {
            if($type == 'get') return $this->getTimeC()->format(DefaultDates::DATE_FORMAT);
            else return $this->setTimeC($this->createDateTimeFromString($props['value']));
        }
        else if ($props['key'] == 'attachs_num') {
            if ($type == 'get') return $this->getAttachsNum();
            else return $this->setAttachsNum($props['value']);
        }
        else if ($props['key'] == 'locations_num') {
            if ($type == 'get') return $this->getLocationsNum();
            else return $this->setLocationsNum($props['value']);
        }
        else if ($props['key'] == 'num_of_replies') {
            if ($type == 'get') return $this->getNumOfReplies();
            else return $this->setNumOfReplies($props['value']);
        }
        else if ($props['key'] == 'num_of_approves') {
            if ($type == 'get') return $this->getNumOfApproves();
            else return $this->setNumOfApproves($props['value']);
        }
    }

}