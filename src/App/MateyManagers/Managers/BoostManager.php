<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.15
 */

namespace App\MateyModels;


class BoostManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Boost';
    }

    public function getTableName() {
        return self::T_BOOST;
    }

    public function getKeyName()
    {
        return "BOOST";
    }

}