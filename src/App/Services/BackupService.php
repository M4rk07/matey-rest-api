<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 24.10.16.
 * Time: 22.31
 */

namespace App\Services;


class BackupService extends BaseService
{

    // RETURNS ASSOC ARRAY WITH USER ATRIBUTES
    public function loadUserIdByUsername($username) {
        $user = $this->db->fetchAll(
            "SELECT user_id 
            FROM ".self::T_USER."
            WHERE email = ? LIMIT 1",
            array($username)
        );
        return $user[0];
    }

}