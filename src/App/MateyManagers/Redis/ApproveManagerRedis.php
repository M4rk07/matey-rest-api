<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.30
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;

class ApproveManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "APPROVE";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\Approve';
    }

}