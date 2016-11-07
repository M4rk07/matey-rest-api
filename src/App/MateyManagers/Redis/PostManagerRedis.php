<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.29
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;

class PostManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "POST";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\Post';
    }

}