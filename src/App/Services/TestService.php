<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.16.
 * Time: 18.29
 */

namespace App\Services;


class TestService extends BaseService
{

    public function insertDepth0 ($interest_0_id, $interest) {

        $this->db->executeUpdate("INSERT IGNORE INTO ".self::T_INTEREST_DEPTH_0." (interest_0_id, interest) VALUES (?, ?)",
            array($interest_0_id, $interest));

    }

    public function insertDepth1 ($interest_1_id, $interest_0_id, $interest) {

        $this->db->executeUpdate("INSERT IGNORE INTO ".self::T_INTEREST_DEPTH_1." (interest_1_id, interest_0_id, interest) VALUES (?, ?, ?)",
            array($interest_1_id, $interest_0_id, $interest));

    }
    public function insertDepth2 ($interest_2_id, $interest_1_id, $interest) {

        $this->db->executeUpdate("INSERT IGNORE INTO ".self::T_INTEREST_DEPTH_2." (interest_2_id, interest_1_id, interest) VALUES (?, ?, ?)",
            array($interest_2_id, $interest_1_id, $interest));

    }

    public function insertDepth3 ($interest_3_id, $interest_2_id, $interest) {


        $this->db->executeUpdate("INSERT IGNORE INTO ".self::T_INTEREST_DEPTH_3." (interest_3_id, interest_2_id, interest) VALUES (?, ?, ?)",
            array($interest_3_id, $interest_2_id, $interest));

    }


}