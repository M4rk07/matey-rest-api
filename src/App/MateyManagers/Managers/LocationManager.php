<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.18
 */

namespace App\MateyModels;


class LocationManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Location';
    }

    public function getTableName() {
        return self::T_LOCATION;
    }

    public function getKeyName()
    {
        return "LOCATION";
    }

}