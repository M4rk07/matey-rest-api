<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 23.21
 */

namespace App\Services;

class RegistrationService extends BaseService
{

    public function storeUserData($email, $first_name, $last_name) {

        $this->db->insert(self::T_USER, array(
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name
        ));

        $this->db->lastInsertId();

        return $this->db->lastInsertId();

    }

    public function storeUserCredentialsData($user_id, $email, $encodedPassword, $salt) {

        $this->db->insert(self::T_A_USER, array(
            'user_id' => $user_id,
            'username' => $email,
            'password' => $encodedPassword,
            'salt' => $salt
        ));

    }

    public function storeFacebookData($newUserId, $fbId) {

        $this->db->insert(self::T_FACEBOOK_INFO, array(
            'user_id' => $newUserId,
            'fb_id' => $fbId
        ));

        return $this->db->lastInsertId();

    }

    public function userExists($email) {
        $result = $this->db->fetchAll("SELECT m_user.user_id, o_user.username, m_f_user.fb_id
        FROM ".self::T_USER." as m_user
        LEFT JOIN ".self::T_A_USER." as o_user USING(user_id)
        LEFT JOIN ".self::T_FACEBOOK_INFO." as m_f_user USING(user_id)
        WHERE email = ? LIMIT 1",
            array($email));

        if(empty($result)) return false;
        return $result[0];
    }

    public function userFbAccountExists ($user_id) {

        $result = $this->db->fetchAll("SELECT fb_id FROM ".self::T_FACEBOOK_INFO." WHERE user_id = ? LIMIT 1",
            array($user_id));

        if(empty($result)) return false;
        return true;
    }

    public function userCredentialsExists($email) {

        $result = $this->db->fetchAll("SELECT password FROM " . self::T_A_USER . " WHERE username = ? LIMIT 1",
            array($email));

        if(empty($result)) return false;
        return true;
    }

    public function registerDevice($gcm, $deviceSecret) {

        $this->db->insert(self::T_DEVICE, array(
            'gcm' => $gcm,
            'device_secret' => $deviceSecret
        ));
        return $this->db->lastInsertId();
    }

    public function updateDevice($device_id, $gcm, $old_gcm) {
        return $this->db->update(self::T_DEVICE, array(
            'gcm' => $gcm
        ), array(
            'device_id' => $device_id,
            'gcm' => $old_gcm
        ));
    }

}