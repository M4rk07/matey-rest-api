<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.3.17.
 * Time: 19.13
 */

namespace App\MateyModels;


class ActivityTypeManager extends AbstractManager
{
    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Approve';
    }

    public function getTableName() {
        return self::T_APPROVE;
    }

    public function getKeyName()
    {
        return "APPROVE";
    }
}