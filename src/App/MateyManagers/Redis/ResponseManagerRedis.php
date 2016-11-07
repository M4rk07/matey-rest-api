<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.28
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;

class ResponseManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "RESPONSE";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\Response';
    }

}