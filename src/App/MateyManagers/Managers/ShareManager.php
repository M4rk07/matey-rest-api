<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.20
 */

namespace App\MateyModels;


class ShareManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Share';
    }

    public function getTableName() {
        return self::T_SHARE;
    }

    public function getKeyName()
    {
        return "SHARE";
    }

}