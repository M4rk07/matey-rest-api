<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 15.00
 */

namespace App\MateyModels;


use App\MateyModels\Login;
use App\Services\BaseService;

class LoginManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Login';
    }

    public function getTableName() {
        return self::T_LOGIN;
    }

}