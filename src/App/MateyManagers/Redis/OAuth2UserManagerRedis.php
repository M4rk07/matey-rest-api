<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 22.30
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;

class OAuth2UserManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "OAUTH2_USER";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\OAuth2User';
    }

}