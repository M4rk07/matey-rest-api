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