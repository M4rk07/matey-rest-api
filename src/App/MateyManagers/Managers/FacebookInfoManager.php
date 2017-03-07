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

    public function createModel(ModelInterface $model, $ignore = false)
    {
        $model = parent::createModel($model, $ignore);
        $this->pushFbAccessToken($model);

        return $model;
    }

    public function pushFbAccessToken(ModelInterface $facebookInfo) {
        $this->redis->set($this->getRedisKey().":".self::SUBKEY_FB_TOKEN.":".$facebookInfo->getId(), $facebookInfo->getFbToken());
        $this->redis->expire($this->getRedisKey().":".self::SUBKEY_FB_TOKEN.":".$facebookInfo->getId(), 3600);
    }

}