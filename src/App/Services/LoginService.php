<?php

namespace App\Services;
use App\OAuth2Models\UserStandard;
use AuthBucket\OAuth2\Exception\InvalidRequestException;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.10.16.
 * Time: 00.54
 */
class LoginService extends BaseService
{

    public function storeLoginRecord ($deviceId, $user_id) {

        $userData = $this->db->fetchAll("SELECT * FROM " . self::T_USER . " WHERE user_id = ? LIMIT 1",
            array($user_id));

        if(empty($userData)) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $this->db->executeUpdate("INSERT INTO " . self::T_LOGIN . " (user_id, device_id) VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE user_id = ?, status = 1, time_logged = NOW()",
            array($user_id, $deviceId, $user_id));

        $userData = $userData[0];

        return $userData;

    }

    public function storeLogoutRecord ($device_id, $user_id) {

        $this->db->executeUpdate("UPDATE " . self::T_LOGIN . " SET status = 0 WHERE device_id = ? AND user_id = ?",
            array($device_id, $user_id));

    }

    public function invalidateAccessToken($accessToken) {

        $this->db->executeUpdate("UPDATE ".self::T_A_ACCESS_TOKEN." SET expires = NOW() WHERE access_token = ?",
            array($accessToken));

    }

}