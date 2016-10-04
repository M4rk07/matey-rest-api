<?php

namespace App\Services;
use AuthBucket\OAuth2\Exception\InvalidRequestException;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.10.16.
 * Time: 00.54
 */
class LoginService extends BaseService
{

    public function loginUser ($deviceId, $username) {

        $userData = $this->db->fetchAll("SELECT id_user, first_name, last_name FROM " . self::T_USER . " WHERE email = ? LIMIT 1",
            array($username));

        $this->db->executeUpdate("INSERT INTO " . self::T_LOGIN . " (id_user, id_device) VALUES (?, ?) ON DUPLICATE KEY UPDATE status = 1",
            array($userData[0]['id_user'], $deviceId));

        return $userData[0];

    }

}