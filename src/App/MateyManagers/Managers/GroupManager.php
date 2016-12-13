<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 22.18
 */

namespace App\MateyModels;


class GroupManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Group';
    }

    public function getTableName() {
        return self::T_GROUP;
    }

    public function getKeyName()
    {
        return "GROUP";
    }

}