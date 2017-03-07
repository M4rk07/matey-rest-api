<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 19.58
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;

class Bookmark extends AbstractModel
{
    protected $userId;
    protected $postId;
    protected $timeC;

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
    public function getTimeC()
    {
        return $this->timeC;
    }

    /**
     * @param mixed $timeC
     */
    public function setTimeC($timeC)
    {
        $this->timeC = $this->createDateTimeFromString($timeC);
        return $this;
    }

    public function setValuesFromArray($values)
    {
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['post_id'])) $this->setPostId($values['post_id']);
        if(isset($values['time_c'])) $this->setUserId($values['time_c']);
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->postId) ? : $keyValues['post_id'] = $this->postId;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->getTimeC()->format(DefaultDates::DATE_FORMAT);

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        return $keyValues;
    }


}