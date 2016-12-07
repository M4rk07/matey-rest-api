<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 15.00
 */

namespace App\MateyModels;


use App\Algos\ActivityWeights;
use App\Algos\Timer;
use App\MateyModels\Follow;
use App\MateyModels\User;
use App\Services\BaseService;

class FollowManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Follow';
    }

    public function getTableName() {
        return self::T_FOLLOWER;
    }

    public function getKeyName()
    {
        return "FOLLOW";
    }
}