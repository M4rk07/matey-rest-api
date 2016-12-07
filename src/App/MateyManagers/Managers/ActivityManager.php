<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.13
 */

namespace App\MateyModels;


use App\MateyModels\Activity;
use App\MateyModels\User;
use App\Services\BaseService;

class ActivityManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Activity';
    }

    public function getTableName() {
        return self::T_ACTIVITY;
    }

    public function getKeyName()
    {
        return "ACTIVITY";
    }

}