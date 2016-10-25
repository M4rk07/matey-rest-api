<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.16.
 * Time: 15.15
 */

namespace App\Services;


class InterestService extends BaseService
{

    public function createUserSubinterest($user_id, $subinterest_id, $score) {

        return $this->db->executeUpdate("INSERT INTO ".self::T_USER_SUBINTEREST." (user_id, subinterest_id, score) VALUES (?,?,?)
        ON DUPLICATE KEY UPDATE score = ?",
            array($user_id, $subinterest_id, $score, $score));

    }

}