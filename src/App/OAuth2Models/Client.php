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

    public function getSetFunction (array $props, $type = 'get')
    {
        if ($props['key'] == 'client_id') {
            if ($type == 'get') return $this->getClientId();
            else return $this->setClientId($props['value']);
        } else if ($props['key'] == 'client_secret') {
            if ($type == 'get') return $this->getClientSecret();
            else return $this->setClientSecret($props['value']);
        } else if ($props['key'] == 'app_name') {
            if ($type == 'get') return $this->getAppName();
            else return $this->setAppName($props['value']);
        } else if ($props['key'] == 'client_type') {
            if ($type == 'get') return $this->getClientType();
            else return $this->setClientType($props['value']);
        } else if ($props['key'] == 'redirect_uri') {
            if ($type == 'get') return $this->getRedirectUri();
            else return $this->setRedirectUri($props['value']);
        }
    }

}