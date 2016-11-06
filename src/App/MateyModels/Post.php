<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 12.55
 */

namespace App\MateyModels;


use App\OAuth2Models\AbstractModel;
use AuthBucket\OAuth2\Model\ModelInterface;

class Post  extends AbstractModel
{

    protected $postId;
    protected $userId;
    protected $text;
    protected $dateTime;

    protected $responses = array();
    protected $bestResponse;
    protected $lastThreeResponses = array();
    protected $numOfResponses;
    protected $numOfShares;

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
    public function getNumOfResponses()
    {
        return $this->numOfResponses;
    }

    /**
     * @param mixed $numOfResponses
     */
    public function setNumOfResponses($numOfResponses)
    {
        $this->numOfResponses = $numOfResponses;
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
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @param mixed $responses
     */
    public function setResponses(array $responses)
    {
        $this->responses = $responses;
        return $this;
    }

    /**
     * @return array
     */
    public function getBestResponse()
    {
        return $this->bestResponse;
    }

    /**
     * @param array $bestResponse
     */
    public function setBestResponse($bestResponse)
    {
        $this->bestResponse = $bestResponse;
        return $this;
    }

    /**
     * @return array
     */
    public function getLastThreeResponses()
    {
        return $this->lastThreeResponses;
    }

    /**
     * @param array $lastThreeResponses
     */
    public function setLastThreeResponses(array $lastThreeResponses)
    {
        $this->lastThreeResponses = $lastThreeResponses;
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
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'text' => $this->text
        ));
    }

    public function unserialize($data) {
        $data = unserialize($data);
        $this->setPostId($data['post_id'])
            ->setUserId($data['user_id'])
            ->setText($data['text']);
        return $this;
    }

    public function setValuesFromArray($values)
    {
        $this->postId = $values['post_id'];
        $this->userId = $values['user_id'];
        $this->text = $values['text'];
        $this->dateTime = $values['date_time'];
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array ();

        empty($this->postId) ? : $keyValues['post_id'] = $this->postId;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->text) ? : $keyValues['text'] = $this->text;
        empty($this->dateTime) ? : $keyValues['date_time'] = $this->dateTime;

        return $keyValues;
    }


}