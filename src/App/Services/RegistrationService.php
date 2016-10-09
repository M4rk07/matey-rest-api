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

    }

    public function userExists($email) {

        $result = $this->db->fetchAll("SELECT email FROM " . self::T_USER . " WHERE email = ? LIMIT 1",
            array($email));

        if(empty($result)) return false;
        return true;

    }

}