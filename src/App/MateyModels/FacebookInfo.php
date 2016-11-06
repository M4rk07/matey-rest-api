<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.38
 */

namespace App\MateyModels;


use App\OAuth2Models\AbstractModel;
use AuthBucket\OAuth2\Model\ModelInterface;

class FacebookInfo extends AbstractModel
{

    protected $userId;
    protected $fbId;
    protected $fbToken;

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

    public function setValuesFromArray($values)
    {
        $this->userId = $values['user_id'];
        $this->fbId = $values['fb_id'];
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array ();

        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->fbId) ? : $keyValues['fb_id'] = $this->fbId;

        return $keyValues;
    }


}