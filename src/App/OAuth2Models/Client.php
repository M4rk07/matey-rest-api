<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:36 PM
 */

namespace App\OAuth2Models;


use AuthBucket\OAuth2\Model\ClientInterface;
use AuthBucket\OAuth2\Model\ModelInterface;

class Client extends AbstractModel implements ClientInterface
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;

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
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param mixed $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
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


    public function setValuesFromArray($values)
    {
        $this->id = $values['id'];

        $this->clientId = $values['client_id'];
        $this->clientSecret = $values['client_secret'];
        $this->redirectUri = $values['redirect_uri'];
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array (
            'id' => $model->getId(),
            'client_id' => $model->getClientId(),
            'client_secret' => $model->getClientSecret(),
            'redirect_uri' => $model->getRedirectUri()
        );

        return $keyValues;
    }

}