<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 1:14 AM
 */

namespace App\OAuth2Models;

use App\MateyModels\AbstractManager;
use AuthBucket\OAuth2\Model\AccessTokenManagerInterface;

class AccessTokenManager extends AbstractManager implements AccessTokenManagerInterface
{


    public function updateToken ($accessToken) {
        $this->db->update($this->getTableName(), array(
            'expires' => (new \DateTime('+60 days'))->format('Y-m-d H:i:s')
        ), array(
            'access_token' => $accessToken
        ));
    }

}