<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.38
 */

namespace App\MateyModels;


use AuthBucket\OAuth2\Model\ModelInterface;

class FacebookInfo extends AbstractModel
{

    protected $userId;
    protected $fbId;
    protected $fbToken;

    public function setId($id) {
        return $this->setUserId($id);
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
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFbToken()
    {
        return $this->fbToken;
    }

    /**
     * @param mixed $fbToken
     */
    public function setFbToken($fbToken)
    {
        $this->fbToken = $fbToken;
        return $this;
    }

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'fb_id') {
            if($type == 'get') return $this->getFbId();
            else return $this->setFbId($props['value']);
        }
        else if($props['key'] == 'fb_token') {
            if($type == 'get') return $this->getFbToken();
            else return $this->setFbToken($props['value']);
        }
    }


}