<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 12.56
 */

namespace App\MateyModels;


use App\MateyModels\Post;
use App\MateyModels\User;
use App\Services\BaseService;

class PostManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Post';
    }

    public function getTableName() {
        return self::T_POST;
    }

    public function getKeyName()
    {
        return "POST";
    }

}