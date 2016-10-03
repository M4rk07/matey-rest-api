<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 5:03 PM
 */

namespace App\OAuth2Models;


use AuthBucket\OAuth2\Model\RefreshTokenManagerInterface;

class RefreshTokenManager extends AbstractManager implements RefreshTokenManagerInterface
{
    public function __construct () {
        parent::__construct();
        $this->tableName = self::T_A_REFRESH_TOKEN;
        $this->className = 'App\\OAuth2Models\\RefreshToken';
        $this->identifier = "refresh_token";
    }
}