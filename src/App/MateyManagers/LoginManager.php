<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 15.00
 */

namespace App\MateyManagers;


use App\MateyModels\Login;
use App\Services\BaseService;

class LoginManager extends BaseService
{

    public function createLogin(Login $login) {

        $this->db->executeUpdate("INSERT INTO ".self::T_LOGIN." (user_id, device_id, gcm, time_logged) VALUES(?,?,?,?)
        ON DUPLICATE KEY UPDATE user_id = ?, status = 1, gcm = ?, time_logged = ?",
            array($login->getUserId(), $login->getDeviceId(), $login->getGcm(), $login->getDateTime(),
                $login->getUserId(), $login->getGcm(), $login->getDateTime()));

    }

    public function deleteLogin(Login $login) {
        $this->db->executeUpdate("UPDATE ".self::T_LOGIN." SET status = 0 WHERE device_id = ? AND user_id = ?",
            array($login->getDeviceId(), $login->getUserId()));
    }

}