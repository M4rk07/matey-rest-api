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

    public function storeLoginRecord ($deviceId, $email, $gcm) {

        $userData = $this->db->fetchAll("SELECT * FROM " . self::T_USER . " WHERE email = ? LIMIT 1",
            array($email));

        if(empty($userData)) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $this->db->executeUpdate("INSERT INTO " . self::T_LOGIN . " (id_user, id_device) VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE id_user = ?, status = 1, time_logged = NOW()",
            array($userData[0]['id_user'], $deviceId, $userData[0]['id_user']));

        $userData = $userData[0];

        return $userData;

    }

}