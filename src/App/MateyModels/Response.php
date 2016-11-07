<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.13
 */

namespace App\MateyModels;


use App\OAuth2Models\AbstractModel;
use AuthBucket\OAuth2\Model\ModelInterface;

class Response  extends AbstractModel
{

    protected $responseId;
    protected $postId;
    protected $userId;
    protected $text;
    protected $dateTime;
    protected $usersApproved = array();
    protected $numOfApproves;

    /**
     * @return mixed
     */
    public function getResponseId()
    {
        return $this->responseId;
    }

    /**
     * @param mixed $responseId
     */
    public function setResponseId($responseId)
    {
        $this->responseId = $responseId;
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
     * @return array
     */
    public function getUsersApproved()
    {
        return $this->usersApproved;
    }

    /**
     * @param array $usersApproved
     */
    public function setUsersApproved($usersApproved)
    {
        foreach ($usersApproved as $user) {
            if(!($user instanceof User)) return false;
        }
        $this->usersApproved = $usersApproved;
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
        $this->responseId = isset($values['response_id']) ? $values['response_id'] : "";
        $this->userId = isset($values['user_id']) ? $values['user_id'] : "";
        $this->postId = isset($values['post_id']) ? $values['post_id'] : "";
        $this->text = isset($values['text']) ? $values['text'] : "";
        $this->dateTime = isset($values['date_time']) ? $values['date_time'] : "";
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array ();

        empty($this->responseId) ? : $keyValues['response_id'] = $this->responseId;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->postId) ? : $keyValues['post_id'] = $this->postId;
        empty($this->text) ? : $keyValues['text'] = $this->text;
        empty($this->dateTime) ? : $keyValues['date_time'] =$this->dateTime;

        return $keyValues;
    }


}