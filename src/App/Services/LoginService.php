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

    public function storeLoginRecord ($deviceId, $user_id, $gcm) {

        $userData = $this->db->fetchAll("SELECT * FROM " . self::T_USER . " WHERE user_id = ? LIMIT 1",
            array($user_id));

        if(empty($userData)) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $this->db->executeUpdate("INSERT INTO " . self::T_LOGIN . " (user_id, device_id, gcm) VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE user_id = ?, status = 1, time_logged = NOW(), gcm = ?",
            array($user_id, $deviceId, $gcm, $user_id, $gcm));

        $userData = $userData[0];

        return $userData;

    }

    public function getDeviceGcm($device_id){

        $result = $this->db->fetchAll("SELECT gcm FROM ".self::T_DEVICE." WHERE device_id = ? LIMIT 1",
            array($device_id));

        return $result[0]['gcm'];

    }

    public function storeLogoutRecord ($device_id, $user_id) {

        return $this->db->executeUpdate("UPDATE " . self::T_LOGIN . " SET status = 0 WHERE device_id = ? AND user_id = ?",
            array($device_id, $user_id));

    }

    public function invalidateAccessToken($accessToken) {

        $this->db->executeUpdate("UPDATE ".self::T_A_ACCESS_TOKEN." SET expires = NOW() WHERE access_token = ?",
            array($accessToken));

    }

}