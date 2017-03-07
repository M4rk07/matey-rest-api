<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:52 PM
 */

namespace App\OAuth2Models;


use App\Constants\Defaults\DefaultDates;
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


    public function getSetFunction (array $props, $type = 'get')
    {
        if ($props['key'] == 'refresh_token') {
            if ($type == 'get') return $this->getRefreshToken();
            else return $this->setRefreshToken($props['value']);
        } else if ($props['key'] == 'client_id') {
            if ($type == 'get') return $this->getClientId();
            else return $this->setClientId($props['value']);
        } else if ($props['key'] == 'username') {
            if ($type == 'get') return $this->getUsername();
            else return $this->setUsername($props['value']);
        } else if ($props['key'] == 'expires') {
            if ($type == 'get') return $this->getExpires()->format(DefaultDates::DATE_FORMAT);
            else return $this->setExpires($this->createDateTimeFromString($props['value']));
        } else if ($props['key'] == 'scope') {
            if ($type == 'get') return $this->getScope();
            else return $this->setScope($props['value']);
        }
    }


}