<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 01.15
 */

namespace App\MateyModels;


use App\MateyModels\Device;
use App\Services\BaseService;

class DeviceManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Device';
    }

    public function getTableName() {
        return self::T_DEVICE;
    }

    public function getKeyName()
    {
        return "DEVICE";
    }

}