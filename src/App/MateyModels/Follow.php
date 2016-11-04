<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 14.58
 */

namespace App\MateyModels;


class Follow extends MateyModel
{

    protected $userFrom;
    protected $userTo;
    protected $dateTime;

    /**
     * @return mixed
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * @param mixed $userFrom
     */
    public function setUserFrom($userFrom)
    {
        $this->userFrom = $userFrom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserTo()
    {
        return $this->userTo;
    }

    /**
     * @param mixed $userTo
     */
    public function setUserTo($userTo)
    {
        $this->userTo = $userTo;
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
        return $this;
    }



}