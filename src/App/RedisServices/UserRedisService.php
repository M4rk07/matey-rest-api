<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 27.10.16.
 * Time: 15.52
 */

namespace App\Services\Redis;


class UserRedisService extends RedisService
{

    public function calculateUserSimilarity($user_one, $user_two) {
        $this->makeMutualConnections($user_one, $user_two);
        $this->makeMutualInterests($user_one, $user_two);
        $score = 0;
        $score += $this->getMutualConnections($user_one, $user_two);
        $score += $this->getMutualInterests($user_one, $user_two);

        $this->similarityScore($user_one, $user_two, $score);

    }

    public function makeMutualConnections($user_one, $user_two) {
        $this->redis->sinterstore(self::KEY_USER.":".self::SUBKEY_MUTUAL."-connections:".$user_one.":".$user_two, array(
            self::KEY_USER.":".self::SUBKEY_CONNECTIONS.":".$user_one,
            self::KEY_USER.":".self::SUBKEY_CONNECTIONS.":".$user_two
        ));
    }
    public function makeMutualInterests($user_one, $user_two) {
        $this->redis->zinterstore(self::KEY_USER.":".self::SUBKEY_MUTUAL."-interests:".$user_one.":".$user_two, array(
            self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_one,
            self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_two
        ));
    }

    public function getMutualConnections($user_one, $user_two) {
        return intval(count($this->redis->smembers(self::KEY_USER.":".self::SUBKEY_MUTUAL."-connections:".$user_one.":".$user_two)));
    }

    public function getMutualInterests($user_one, $user_two) {
        return intval(count($this->redis->zrange(self::KEY_USER.":".self::SUBKEY_MUTUAL."-interests:".$user_one.":".$user_two, 0, -1)));
    }

    public function similarityScore($user_one, $user_two, $score) {
        $this->redis->zadd(self::KEY_USER.":users-similarity:".$user_one, array(
            $user_two => $score
        ));
    }

    public function getUserSimilarityRank($user_one, $user_two) {
        return $this->redis->zrank(self::KEY_USER.":users-similarity:".$user_one, $user_two);
    }

    public function removeSimilarityCalculations($user_one, $result) {
        foreach($result as $res) {
            $this->redis->del(self::KEY_USER.":".self::SUBKEY_MUTUAL."-connections:".$user_one.":".$res['user_id']);
            $this->redis->del(self::KEY_USER.":".self::SUBKEY_MUTUAL."-interests:".$user_one.":".$res['user_id']);
        }
        $this->redis->del(self::KEY_USER.":users-similarity:".$user_one);
    }

}