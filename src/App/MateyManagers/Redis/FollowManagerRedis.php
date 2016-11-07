<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.29
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;

class FollowManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "FOLLOW";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\Follow';
    }

}