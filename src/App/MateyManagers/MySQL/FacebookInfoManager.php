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
    public function __construct () {
        parent::__construct(self::T_FACEBOOK_INFO, 'App\\MateyModels\\FacebookInfo');
    }

}