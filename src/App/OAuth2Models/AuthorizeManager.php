<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:25 PM
 */

namespace App\OAuth2Models;

use AuthBucket\OAuth2\Model\AccessTokenManagerInterface;

class AuthorizeManager extends AbstractManager implements AccessTokenManagerInterface
{
    public function __construct () {
        parent::__construct(self::T_A_AUTHORIZE, 'App\\OAuth2Models\\Authorize', "client_id");
    }
}