<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 1:14 AM
 */

namespace App\OAuth2Models;

use AuthBucket\OAuth2\Model\AccessTokenManagerInterface;

class AccessTokenManager extends AbstractManager implements AccessTokenManagerInterface
{
    public function __construct () {
        parent::__construct();
        $this->tableName = "oauth2_access_tokens";
        $this->className = 'App\\OAuth2Models\\AccessToken';
        $this->identifier = "access_token";
    }

}