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

    public function storeLoginRecord ($deviceId, $email) {

        $userData = $this->db->fetchAll("SELECT * FROM " . self::T_USER . " WHERE email = ? LIMIT 1",
            array($email));

        if(empty($userData)) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $this->db->executeUpdate("INSERT INTO " . self::T_LOGIN . " (user_id, device_id) VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE user_id = ?, status = 1, time_logged = NOW()",
            array($userData[0]['user_id'], $deviceId, $userData[0]['user_id']));

        $userData = $userData[0];

        return $userData;

    }

}