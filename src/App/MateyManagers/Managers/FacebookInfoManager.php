<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.40
 */

namespace App\MateyModels;


use AuthBucket\OAuth2\Model\ModelInterface;

class FacebookInfoManager extends AbstractManager
{
    const SUBKEY_FB_TOKEN = "fb-token";

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

    public function getKeyName()
    {
        return "FACEBOOK_INFO";
    }

    public function createModel(ModelInterface $model, $ignore = false)
    {
        $model = parent::createModel($model, $ignore);
        $this->pushFbAccessToken($model);

        return $model;
    }

    public function pushFbAccessToken(ModelInterface $facebookInfo) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$facebookInfo->getId(), $facebookInfo->getFbToken());
        $this->redis->expire(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$facebookInfo->getId(), 3600);
    }

}