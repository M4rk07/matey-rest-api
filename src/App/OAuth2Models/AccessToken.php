<?php

/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 12:59 AM
 */

namespace App\OAuth2Models;

use App\MateyModels\AbstractModel;
use AuthBucket\OAuth2\Model\AccessTokenInterface;
use AuthBucket\OAuth2\Model\ModelInterface;
use AuthBucket\OAuth2\Exception\ServerErrorException;

class AccessToken extends AbstractModel implements AccessTokenInterface
{

    protected $accessToken;
    protected $tokenType;
    protected $clientId;
    protected $username;
    protected $expires;
    protected $scope;

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @param mixed $tokenType
     */
    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;
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

    // set object values from array
    public function setValuesFromArray ($values) {

        $this->id = $values['access_token'];

        $this->accessToken = $values['access_token'];
        $this->tokenType = $values['token_type'];
        $this->clientId = $values['client_id'];
        $this->username = $values['username'];
        $this->expires = $this->createDateTimeFromString($values['expires']);
        $this->scope = $this->createArrayFromString($values['scope']);

    }

    // gets object values as array
    public function getValuesAsArray () {

        $keyValues = array ();

        empty($this->accessToken) ? : $keyValues['access_token'] = $this->accessToken;
        empty($this->tokenType) ? : $keyValues['token_type'] = $this->tokenType;
        empty($this->clientId) ? : $keyValues['client_id'] = $this->clientId;
        empty($this->username) ? : $keyValues['username'] = $this->username;
        empty($this->expires) ? : $keyValues['expires'] =($this->expires)->format($this->dateFormat);
        empty($this->scope) ? : $keyValues['scope'] = $this->scope;

        return $keyValues;

    }

}