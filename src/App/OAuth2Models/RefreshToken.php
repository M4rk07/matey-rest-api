<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:52 PM
 */

namespace App\OAuth2Models;


use App\MateyModels\AbstractModel;
use AuthBucket\OAuth2\Model\ModelInterface;
use AuthBucket\OAuth2\Model\RefreshTokenInterface;

class RefreshToken extends AbstractModel implements RefreshTokenInterface
{
    protected $refreshToken;
    protected $clientId;
    protected $username;
    protected $expires;
    protected $scope;

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param mixed $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param mixed $scope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }



    public function setValuesFromArray($values)
    {
        $expires = \DateTime::createFromFormat('Y-m-d H:i:s', $values['expires']);

        $this->id = $values['refresh_token'];

        $this->refreshToken = $values['refresh_token'];
        $this->clientId = $values['client_id'];
        $this->username = $values['username'];
        $this->expires = $this->createDateTimeFromString($values['expires']);
        $this->scope = $this->createArrayFromString($values['scope']);
    }

    public function getValuesAsArray()
    {
        $keyValues = array ();

        empty($this->refreshToken) ? : $keyValues['refresh_token'] = $this->refreshToken;
        empty($this->clientId) ? : $keyValues['client_id'] = $this->clientId;
        empty($this->username) ? : $keyValues['username'] = $this->username;
        empty($this->expires) ? : $keyValues['expires'] = $this->expires;
        empty($this->scope) ? : $keyValues['scope'] =$this->scope;

        return $keyValues;
    }


}