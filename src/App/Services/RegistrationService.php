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

    public function storeUserData($userId, $email, $first_name, $last_name, $birth_year) {

        $this->db->insert(self::T_USER, array(
            'id_user' => $userId,
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'birth_year' => $birth_year
        ));

    }

}