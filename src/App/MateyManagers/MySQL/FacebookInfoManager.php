<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.40
 */

namespace App\MateyModels;


class FacebookInfoManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\FacebookInfo';
    }

    public function getTableName() {
        return self::T_FACEBOOK_INFO;
    }

}