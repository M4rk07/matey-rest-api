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

    public function storeUserData($email, $first_name, $last_name, $profilePicture = null) {

        $this->db->executeUpdate("INSERT INTO ".self::T_USER." (email, first_name, last_name, profile_picture) VALUES (?,?,?,?)",
            array($email, $first_name, $last_name, $profilePicture));

        return $this->db->lastInsertId();

    }

    public function storeUserInfo($birthday = null, $gender = null, $hometown = null) {

        $this->db->executeUpdate("INSERT INTO ".self::T_USER_INFO." (birthday, gender, hometown) VALUES (?,?,?)",
            array($birthday, $gender, $hometown));

    }

    public function storeUserCredentialsData($user_id, $email, $encodedPassword, $salt) {

        $this->db->executeUpdate("INSERT INTO ".self::T_A_USER." (user_id, username, password, salt) VALUES (?,?,?,?)",
            array($user_id, $email, $encodedPassword, $salt));

    }

    public function storeFacebookData($newUserId, $fbId) {

        $this->db->executeUpdate("INSERT INTO ".self::T_FACEBOOK_INFO." (user_id, fb_id) VALUES (?,?)",
            array($newUserId, $fbId));

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

        $this->db->executeUpdate("INSERT INTO ".self::T_DEVICE." (gcm, device_secret) VALUES (?,?)",
            array($gcm, $deviceSecret));

        return $this->db->lastInsertId();
    }

    public function updateDevice($device_id, $gcm, $old_gcm) {

        return $this->db->executeUpdate("UPDATE ".self::T_DEVICE." SET gcm = ? WHERE device_id = ? AND gcm = ?",
            array($gcm, $device_id, $old_gcm));

    }

}