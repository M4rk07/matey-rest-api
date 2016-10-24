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
        parent::__construct(self::T_A_ACCESS_TOKEN, 'App\\OAuth2Models\\AccessToken', "access_token");
    }

}