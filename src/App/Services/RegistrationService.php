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

    public function storeUserData($email, $first_name, $last_name) {

        $this->db->insert(self::T_USER, array(
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name
        ));

        $user_id = $this->db->lastInsertId();

        $this->redis->set("user:by_email:".$email, $user_id);
        $this->redis->hmset("user:statistics:".$user_id, array(
            'num_of_posts' => 0,
            'num_of_responses' => 0,
            'num_of_received_approves' => 0,
            'num_of_followers' => 0,
            'num_of_following' => 0
        ));

        return $this->db->lastInsertId();

    }

    public function userExists($email) {

        $result = $this->db->fetchAll("SELECT m_user.user_id, fb_info.fb_id 
        FROM ".self::T_USER." as m_user
        LEFT JOIN ".self::T_FACEBOOK_INFO." as fb_info USING(user_id) 
        WHERE m_user.email = ? LIMIT 1",
            array($email));

        if(empty($result)) return false;

        return $result[0];

    }

    public function registerDevice($gcm) {

        $this->db->insert(self::T_DEVICE, array(
            'gcm' => $gcm,
        ));
        return $this->db->lastInsertId();
    }

}