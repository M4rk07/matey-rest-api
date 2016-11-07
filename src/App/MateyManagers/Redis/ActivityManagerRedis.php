<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.30
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;

class ActivityManagerRedis extends AbstractManagerRedis
{
    public function getKeyName()
    {
        return "ACTIVITY";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\Activity';
    }

}