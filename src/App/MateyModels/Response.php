<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.13
 */

namespace App\MateyModels;


class Response  extends MateyModel
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
        return $this->serialize(array(
            'response_id' => $this->responseId,
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'text' => $this->text,
            'date_time' => $this->dateTime
        ));
    }

}