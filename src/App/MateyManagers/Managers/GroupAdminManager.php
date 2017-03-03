<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.16
 */

namespace App\MateyModels;


class GroupAdminManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\GroupAdmin';
    }

    public function getTableName() {
        return self::T_GROUP_ADMIN;
    }

    public function getKeyName()
    {
        return "GROUP_ADMIN";
    }

}