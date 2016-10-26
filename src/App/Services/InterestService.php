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

    public function createUserInterest($user_id, $interest_id, $depth) {

        return $this->db->executeUpdate("INSERT IGNORE INTO ".self::T_USER_INTEREST." (user_id, interest_id, depth) VALUES (?,?,?)",
            array($user_id, $interest_id, $depth));

    }

    public function findParentDepth_1($interest_id) {

        $result = $this->db->fetchAll("SELECT interest_0_id FROM ".self::T_INTEREST_DEPTH_."1 WHERE interest_1_id = ?",
            array($interest_id));

        if(empty($result)) return false;
        else return $result[0];

    }

    public function findParentDepth_2($interest_id) {

        $result = $this->db->fetchAll("SELECT depth0.interest_0_id, depth1.interest_1_id 
          FROM ".self::T_INTEREST_DEPTH_."2 as depth2
         INNER JOIN ".self::T_INTEREST_DEPTH_."1 as depth1 ON(depth1.interest_1_id = depth2.interest_1_id)
         INNER JOIN ".self::T_INTEREST_DEPTH_."0 as depth0 ON(depth0.interest_0_id = depth1.interest_0_id)
         WHERE depth2.interest_2_id = ? LIMIT 1",
            array($interest_id));

        if(empty($result)) return false;
        else return $result[0];

    }

    public function findParentDepth_3($interest_id) {

        $result = $this->db->fetchAll("SELECT depth0.interest_0_id, depth1.interest_1_id, depth2.interest_2_id 
          FROM ".self::T_INTEREST_DEPTH_."3 as depth3
          INNER JOIN ".self::T_INTEREST_DEPTH_."2 as depth2 ON(depth2.interest_2_id = depth3.interest_2_id)
         INNER JOIN ".self::T_INTEREST_DEPTH_."1 as depth1 ON(depth1.interest_1_id = depth2.interest_1_id)
         INNER JOIN ".self::T_INTEREST_DEPTH_."0 as depth0 ON(depth0.interest_0_id = depth1.interest_0_id)
         WHERE depth3.interest_3_id = ? LIMIT 1",
            array($interest_id));

        if(empty($result)) return false;
        else return $result[0];

    }

}