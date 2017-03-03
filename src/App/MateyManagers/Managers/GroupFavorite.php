<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.17
 */

namespace App\MateyModels;


class GroupFavorite extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\GroupFavorite';
    }

    public function getTableName() {
        return self::T_GROUP_FAVORITE;
    }

    public function getKeyName()
    {
        return "GROUP_FAVORITE";
    }

}