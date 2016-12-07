<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.26
 */

namespace App\MateyModels;


class ApproveManager extends AbstractManager
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