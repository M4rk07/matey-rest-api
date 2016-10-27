<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 27.10.16.
 * Time: 15.25
 */

namespace App\Services;


class SearchService extends BaseService
{

    public function searchByName($name, $user_id) {
        return $this->db->fetchAll("SELECT full_name, user_id, profile_picture FROM ".self::T_USER." WHERE full_name LIKE ? AND user_id != ?",
            array($name."%", $user_id));
    }

}