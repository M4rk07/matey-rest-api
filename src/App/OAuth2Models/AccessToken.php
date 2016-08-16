<?php

/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 12:59 AM
 */

namespace Matey\OAuth2\Models;

use AuthBucket\OAuth2\Model\AccessTokenInterface;

class AccessToken implements AccessTokenInterface
{
    protected $id;
    protected $accessToken;
    protected $tokenType;
    protected $clientId;
    protected $username;
    protected $expires;
    protected $scope;

    public function getId()
    {
        return $this->id;
    }

    public function setAccessToken($accessToken)
    {
        // TODO: Implement setAccessToken() method.
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setTokenType($tokenType)
    {
        // TODO: Implement setTokenType() method.
    }

    public function getTokenType()
    {
        return $this->tokenType;
    }

    public function setClientId($clientId)
    {
        // TODO: Implement setClientId() method.
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setUsername($username)
    {
        // TODO: Implement setUsername() method.
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setExpires($expires)
    {
        // TODO: Implement setExpires() method.
    }

    public function getExpires()
    {
        return $this->expires;
    }

    public function setScope($scope)
    {
        // TODO: Implement setScope() method.
    }

    public function getScope()
    {
        return $this->scope;
    }

}