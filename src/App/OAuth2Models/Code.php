<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:45 PM
 */

namespace App\OAuth2Models;


use AuthBucket\OAuth2\Model\CodeInterface;
use AuthBucket\OAuth2\Model\ModelInterface;

class Code extends AbstractModel implements CodeInterface
{

    protected $code;
    protected $clientId;
    protected $username;
    protected $redirectUri;
    protected $expires;
    protected $scope;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @param mixed $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
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



    public function setValuesFromArray($values)
    {
        $this->code = $values['code'];
        $this->clientId = $values['client_id'];
        $this->username = $values['username'];
        $this->redirectUri = $values['redirect_uri'];
        $this->expires = $values['expires'];
        $this->scope = $values['scope'];
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array (
            'code' => $model->getCode(),
            'client_id' => $model->getClientId(),
            'username' => $model->getUsername(),
            'redirect_uri' => $model->getRedirectUri(),
            'expires' => $model->getExpires(),
            'scope' => $model->getScope()
        );

        return $keyValues;
    }

}