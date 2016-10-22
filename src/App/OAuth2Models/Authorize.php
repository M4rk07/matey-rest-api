<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:20 PM
 */

namespace App\OAuth2Models;


use AuthBucket\OAuth2\Model\AuthorizeInterface;
use AuthBucket\OAuth2\Model\ModelInterface;

class Authorize extends AbstractModel implements AuthorizeInterface
{
    protected $clientId;
    protected $username;
    protected $scope;

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
        $this->id = $values['client_id'];

        $this->clientId = $values['client_id'];
        $this->username = $values['username'];
        $this->scope = $this->createArrayFromString($values['scope']);
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array (
            'client_id' => $model->getClientId(),
            'username' => $model->getUsername(),
            'scope' => $this->createStringFromArray($model->getScope())
        );

        return $keyValues;
    }

}