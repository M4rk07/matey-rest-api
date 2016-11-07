<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 15.00
 */

namespace App\MateyModels;


use App\MateyModels\Login;
use App\Services\BaseService;

class LoginManager extends AbstractManager
{
    public function __construct ($db) {
        parent::__construct($db);
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Login';
    }

    public function getTableName() {
        return self::T_LOGIN;
    }

    public function createLogin(Login $login) {

        $this->db->executeUpdate("INSERT INTO ".self::T_LOGIN." (user_id, device_id, gcm, time_logged) VALUES(?,?,?,?)
        ON DUPLICATE KEY UPDATE user_id = ?, status = 1, gcm = ?, time_logged = ?",
            array($login->getUserId(), $login->getDeviceId(), $login->getGcm(), $login->getDateTime(),
                $login->getUserId(), $login->getGcm(), $login->getDateTime()));

        $this->setLoginGcmToRedis($login);

    }

    public function deleteLogin(Login $login) {
        $this->db->executeUpdate("UPDATE ".self::T_LOGIN." SET status = 0 WHERE device_id = ? AND user_id = ?",
            array($login->getDeviceId(), $login->getUserId()));

        $this->deleteLoginGcmFromRedis($login);
    }

    public function setLoginGcmToRedis (Login $login) {
        $this->redis->sadd(self::KEY_USER.":".self::SUBKEY_LOGIN_GCMS.":".$login->getUserId(), $login->getGcm());
    }

    public function deleteLoginGcmFromRedis(Login $login) {
        $this->redis->del(self::KEY_USER.":".self::SUBKEY_LOGIN_GCMS.":".$login->getUserId());
    }

}