<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.30
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;

class DeviceManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "DEVICE";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\Device';
    }

}