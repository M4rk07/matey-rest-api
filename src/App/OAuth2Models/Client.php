<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:36 PM
 */

namespace App\OAuth2Models;


use App\MateyModels\AbstractModel;
use AuthBucket\OAuth2\Model\ClientInterface;
use AuthBucket\OAuth2\Model\ModelInterface;

class Client extends AbstractModel implements ClientInterface
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $appName;
    protected $clientType;

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

    public function setAppName ($appName) {

        $this->appName = $appName;
        return $this;

    }

    public function getAppName () {

        return $this->appName;

    }

    public function setClientType ($clientType) {

        $this->clientType = $clientType;
        return $this;

    }

    public function getClientType () {

        return $this->clientType;

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
        $this->id = $values['client_id'];

        $this->clientId = $this->id;
        $this->clientSecret = $values['client_secret'];
        $this->appName = $values['app_name'];
        $this->clientType = $values['client_type'];
        $this->redirectUri = $values['redirect_uri'];
    }

    public function getMysqlValues () {
        $keyValues = array ();

        empty($this->clientId) ? : $keyValues['client_id'] = $this->clientId;
        empty($this->clientSecret) ? : $keyValues['client_secret'] = $this->clientSecret;
        empty($this->appName) ? : $keyValues['app_name'] = $this->appName;
        empty($this->clientType) ? : $keyValues['client_type'] = $this->clientType;
        empty($this->redirectUri) ? : $keyValues['redirect_uri'] =$this->redirectUri;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = array ();

        empty($this->clientId) ? : $keyValues['client_id'] = $this->clientId;
        empty($this->clientSecret) ? : $keyValues['client_secret'] = $this->clientSecret;
        empty($this->appName) ? : $keyValues['app_name'] = $this->appName;
        empty($this->clientType) ? : $keyValues['client_type'] = $this->clientType;
        empty($this->redirectUri) ? : $keyValues['redirect_uri'] =$this->redirectUri;

        return $keyValues;
    }

}