<?php

/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 12:59 AM
 */

namespace App\OAuth2Models;

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
    }

    public function setValuesFromArray ($values) {

        $this->accessToken = $values['access_token'];
        $this->tokenType = $values['token_type'];
        $this->clientId = $values['client_id'];
        $this->username = $values['username'];
        $this->expires = $values['expires'];
        $this->scope = $values['scope'];

    }

    public function getValuesAsArray (ModelInterface $model) {

        $keyValues = array (
            'access_token' => $model->getAccessToken(),
            'token_type' => $model->getTokenType(),
            'client_id' => $model->getClientId(),
            'username' => $model->getUsername(),
            'expires' => $model->getExpires(),
            'scope' => $model->getScope()
        );

        return $keyValues;

    }

}