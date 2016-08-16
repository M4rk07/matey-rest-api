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

        $this->id = $values['id'];

        $this->code = $values['code'];
        $this->clientId = $values['client_id'];
        $this->username = $values['username'];
        $this->redirectUri = $values['redirect_uri'];
        $this->expires = $expires;
        $this->scope = explode(" ", $values['scope']);
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array (
            'id' => $model->getId(),
            'code' => $model->getCode(),
            'client_id' => $model->getClientId(),
            'username' => $model->getUsername(),
            'redirect_uri' => $model->getRedirectUri(),
            'expires' => $model->getExpires()->format('Y-m-d H:i:s'),
            'scope' => implode(" ", $model->getScope())
        );

        return $keyValues;
    }

}