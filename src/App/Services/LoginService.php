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

        $userData = $this->db->fetchAll("SELECT m_user.*, f_user.fb_id 
          FROM ".self::T_USER." as m_user
         LEFT JOIN ".self::T_FACEBOOK_INFO." as f_user USING(user_id) 
         WHERE m_user.user_id = ? LIMIT 1",
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

    public function setUserFirstTimeLogged ($user_id) {

        return $this->db->executeUpdate("UPDATE ".self::T_USER." SET first_login = 1 WHERE user_id = ?",
            array($user_id));

    }

    public function findFriendsByFbId($fbIds) {

        $stmt = $this->db->executeQuery("SELECT m_usr.user_id, m_usr.first_name, m_usr.last_name, m_usr.profile_picture FROM ".self::T_FACEBOOK_INFO." as m_f_info
        INNER JOIN ".self::T_USER." as m_usr USING(user_id)
        WHERE m_f_info.fb_id IN(?)",
            array($fbIds),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();
        return $stmt->fetchAll();

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