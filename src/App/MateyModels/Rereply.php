<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.07
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;

class Rereply extends AbstractModel
{

    protected $rereplyId;
    protected $replyId;
    protected $userId;
    protected $text;
    protected $timeC;
    protected $numOfApproves;

    public function setId($id) {
        return $this->setRereplyId($id);
    }
    /**
     * @return mixed
     */
    public function getRereplyId()
    {
        return $this->rereplyId;
    }

    /**
     * @param mixed $rereplyId
     */
    public function setRereplyId($rereplyId)
    {
        $this->rereplyId = $rereplyId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplyId()
    {
        return $this->replyId;
    }

    /**
     * @param mixed $postId
     */
    public function setReplyId($replyId)
    {
        $this->replyId = $replyId;
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
        if ($props['key'] == 'rereply_id') {
            if ($type == 'get') return $this->getRereplyId();
            else return $this->setRereplyId($props['value']);
        }
        if ($props['key'] == 'reply_id') {
            if ($type == 'get') return $this->getReplyId();
            else return $this->setReplyId($props['value']);
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
        else if ($props['key'] == 'num_of_approves') {
            if ($type == 'get') return $this->getNumOfApproves();
            else return $this->setNumOfApproves($props['value']);
        }
    }

}