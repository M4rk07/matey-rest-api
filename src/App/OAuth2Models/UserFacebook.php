<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.10.16.
 * Time: 23.36
 */

namespace App\OAuth2Models;


class FacebookUser extends User
{

    protected $fbId;

    /**
     * @return mixed
     */
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * @param mixed $fbId
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;
    }



}