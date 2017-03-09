<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.00
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;

class Boost extends AbstractModel
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
        $this->timeC = $timeC;
        return $this;
    }

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'post_id') {
            if($type == 'get') return $this->getPostId();
            else return $this->setPostId($props['value']);
        }
        else if($props['key'] == 'time_c') {
            if($type == 'get') return $this->getTimeC();
            else return $this->setTimeC($this->createDateTimeFromString($props['value']));
        }
    }

}