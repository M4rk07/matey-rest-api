<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 5:03 PM
 */

namespace App\OAuth2Models;


use App\MateyModels\AbstractManager;
use AuthBucket\OAuth2\Model\RefreshTokenManagerInterface;

class RefreshTokenManager extends AbstractManager implements RefreshTokenManagerInterface
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\OAuth2Models\\RefreshToken';
    }

    public function getTableName() {
        return self::T_A_REFRESH_TOKEN;
    }

}