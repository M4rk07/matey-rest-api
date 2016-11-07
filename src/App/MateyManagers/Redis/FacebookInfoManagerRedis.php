<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.41
 */

namespace App\MateyModels;


use App\Services\BaseServiceRedis;
use AuthBucket\OAuth2\Model\ModelInterface;

class FacebookInfoManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "FACEBOOK_INFO";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\FacebookInfo';
    }

    public function pushFbAccessToken(ModelInterface $user) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$user->getUserId(), $user->getFbToken());
        $this->redis->expire(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$user->getUserId(), 3600);
    }

}