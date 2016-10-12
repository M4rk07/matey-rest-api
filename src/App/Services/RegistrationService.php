<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 23.21
 */

namespace App\Services;


use Predis\Client;

class RegistrationService extends BaseService
{

    public function storeUserData($email, $first_name, $last_name, $birth_year) {

        $this->db->insert(self::T_USER, array(
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'birth_year' => $birth_year
        ));
        return $this->db->lastInsertId();

    }

    public function userExists($email) {
        $redis = new Client();

        $result = $this->db->fetchAll("SELECT m_user.user_id, fb_info.fb_id 
        FROM ".self::T_USER." as m_user
        LEFT JOIN ".self::T_FACEBOOK_INFO." as fb_info USING(user_id) 
        WHERE m_user.email = ? LIMIT 1",
            array($email));

        if(empty($result)) return false;

        return $result[0];

    }

    public function cacheUser($user_id, $email, $firstName, $lastName, $birthYear, $fb_id = null) {
        $redis = new Client();
        $redis->hmset("user:".$user_id, array(
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birth_year' => $birthYear,
            $fb_id == null ? : 'fb_id' => $fb_id
        ));
        $redis->set("user:by_email:".$email, $user_id);
        $redis->set("user:num_of_posts:".$user_id, 0);
        $redis->set("user:num_of_responses:".$user_id, 0);
    }

    public function registerDevice($gcm) {

        $this->db->insert(self::T_DEVICE, array(
            'gcm' => $gcm,
        ));
        return $this->db->lastInsertId();
    }

}