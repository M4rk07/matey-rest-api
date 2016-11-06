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
class UserManagerRedis extends BaseServiceRedis
{

    public function initializeUserStatistics(User $user) {
        $this->redis->hmset(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getUserId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_FOLLOWING => 0,
            self::FIELD_NUM_OF_POSTS => 0,
            self::FIELD_NUM_OF_GIVEN_APPROVES => 0,
            self::FIELD_NUM_OF_RECEIVED_APPROVES => 0,
            self::FIELD_NUM_OF_GIVEN_RESPONSES => 0,
            self::FIELD_NUM_OF_RECEIVED_RESPONSES => 0,
            self::FIELD_NUM_OF_BEST_RESPONSES => 0,
            self::FIELD_NUM_OF_PROFILE_CLICKS => 0,
            self::FILED_NUM_OF_SHARES => 0
        ));
    }

    public function initializeUserIdByEmail (User $user) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_USER_ID.":".$user->getUsername(), $user->getUserId());
    }

}