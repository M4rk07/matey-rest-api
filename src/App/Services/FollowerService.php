<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.13
 */

namespace App\Services;


class FollowerService extends BaseService
{

    public function createFollow($fromUser, $toUser) {

        $this->db->insert(self::T_FOLLOWER, array(
            'from_user' => $fromUser,
            'to_user' => $toUser
        ));

        // UPDATE STATISTICS
        $this->redis->hincrby("user:statistics:".$fromUser, "num_of_following", 1);
        $this->redis->hincrby("user:statistics:".$toUser, "num_of_followers", 1);

    }

    public function deleteFollow($fromUser, $toUser) {

        $this->db->delete(self::T_FOLLOWER, array(
            'from_user' => $fromUser,
            'to_user' => $toUser
        ));

        $this->redis->hincrby("user:statistics:".$fromUser, "num_of_following", -1);
        $this->redis->hincrby("user:statistics:".$toUser, "num_of_followers", -1);

    }

    public function returnFollowers ($ofUser) {

        return $this->db->fetchAll("SELECT flw.from_user FROM ".self::T_FOLLOWER." as flw WHERE to_user = ?",
            array($ofUser));

    }

}