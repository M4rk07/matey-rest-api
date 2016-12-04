<?php

namespace App\MateyModels;
use App\MateyModels\User;
use App\Services\BaseServiceRedis;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 15.45
 */
class UserManagerRedis extends AbstractManagerRedis
{

    public function getKeyName()
    {
        return "USER";
    }

    public function getClassName()
    {
        return 'App\\MateyModels\\User';
    }

    public function initializeUserStatistics(User $user) {
        $this->redis->hmset(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_FOLLOWING => 0,
            self::FIELD_NUM_OF_POSTS => 0,
            self::FIELD_NUM_OF_GIVEN_APPROVES => 0,
            self::FIELD_NUM_OF_RECEIVED_APPROVES => 0,
            self::FIELD_NUM_OF_GIVEN_RESPONSES => 0,
            self::FIELD_NUM_OF_RECEIVED_RESPONSES => 0,
            self::FIELD_NUM_OF_BEST_RESPONSES => 0,
            self::FIELD_NUM_OF_SHARES => 0
        ));
    }

    public function getUserStatistics (User $user) {

        $userStatistics = $this->redis->hgetall(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId());

        $user->setValuesFromArray($userStatistics);

        return $user;

    }

    public function incrNumOfFollowers(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_FOLLOWERS, $incrBy);
    }

    public function decrNumOfFollowers(User $user, $decrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_FOLLOWERS, $decrBy);
    }

    public function incrNumOfFollowing(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_FOLLOWING, $incrBy);
    }

    public function decrNumOfFollowing(User $user, $decrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_FOLLOWING, $decrBy);
    }

    public function incrNumOfPosts(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_POSTS, $incrBy);
    }

    public function incrNumOfGivenApproves(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_GIVEN_APPROVES, $incrBy);
    }

    public function incrNumOfReceivedApproves(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_RECEIVED_APPROVES, $incrBy);
    }

    public function incrNumOfGivenResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_GIVEN_RESPONSES, $incrBy);
    }

    public function incrNumOfReceivedResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_RECEIVED_RESPONSES, $incrBy);
    }

    public function incrNumOfBestResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_BEST_RESPONSES, $incrBy);
    }

    public function incrNumOfShares(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_SHARES, $incrBy);
    }

    public function initializeUserIdByEmail (User $user) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_USER_ID.":".$user->getEmail(), $user->getId());
    }

    public function getUserIdByEmail ($email) {
        return $this->redis->get(self::KEY_USER.":".self::SUBKEY_USER_ID.":".$email);
    }

}