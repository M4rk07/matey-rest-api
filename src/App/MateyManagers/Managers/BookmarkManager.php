<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.14
 */

namespace App\MateyModels;


class BookmarkManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Bookmark';
    }

    public function getTableName() {
        return self::T_BOOKMARK;
    }

    public function getKeyName()
    {
        return "BOOKMARK";
    }

}