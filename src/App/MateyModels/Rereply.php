<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.07
 */

namespace App\MateyModels;


class Rereply extends AbstractModel
{

    protected $replyId;
    protected $userId;
    protected $text;
    protected $timeC;
    protected $numOfApproves;

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

    public function setValuesFromArray($values)
    {
        if(isset($values['rereply_id'])) $this->setId($values['rereply_id']);
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['reply_id'])) $this->setReplyId($values['reply_id']);
        if(isset($values['text'])) $this->setText($values['text']);
        if(isset($values['time_c'])) $this->setTimeC($values['time_c']);
        if(isset($values['num_of_approves'])) $this->setNumOfApproves($values['num_of_approves']);
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['rereply_id'] = $this->id;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->replyId) ? : $keyValues['reply_id'] = $this->replyId;
        empty($this->text) ? : $keyValues['text'] = $this->text;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->timeC;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        empty($this->numOfApproves) ? : $keyValues['num_of_approves'] = $this->numOfApproves;

        return $keyValues;
    }

}