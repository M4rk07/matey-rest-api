<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:25 PM
 */

namespace App\OAuth2Models;

use App\MateyModels\AbstractManager;
use AuthBucket\OAuth2\Model\AccessTokenManagerInterface;

class AuthorizeManager extends AbstractManager implements AccessTokenManagerInterface
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\OAuth2Models\\Authorize';
    }

    public function getTableName() {
        return self::T_A_AUTHORIZE;
    }

}