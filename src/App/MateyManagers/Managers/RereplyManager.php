<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.19
 */

namespace App\MateyModels;


class RereplyManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Rereply';
    }

    public function getTableName() {
        return self::T_REREPLY;
    }

    public function getKeyName()
    {
        return "REREPLY";
    }

}