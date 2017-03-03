<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.55
 */

namespace App\MateyModels;


use App\MateyModels\Response;
use App\MateyModels\User;
use App\Services\BaseService;

class ResponseManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Reply';
    }

    public function getTableName() {
        return self::T_REPLY;
    }

    public function getKeyName()
    {
        return "REPLY";
    }

}