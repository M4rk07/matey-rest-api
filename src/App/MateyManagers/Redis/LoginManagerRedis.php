<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.29
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;

class LoginManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "LOGIN";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\Login';
    }

}