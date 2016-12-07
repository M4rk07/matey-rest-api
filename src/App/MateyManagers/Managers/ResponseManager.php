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
        return 'App\\MateyModels\\Response';
    }

    public function getTableName() {
        return self::T_RESPONSE;
    }

    public function getKeyName()
    {
        return "RESPONSE";
    }

}