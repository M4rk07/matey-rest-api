<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 17.46
 */

namespace App\MateyManagers;


use App\Algos\Timer;
use App\MateyModels\Activity;
use App\MateyModels\User;
use App\Services\BaseService;

class NewsfeedManager extends BaseService
{

    public function pushActivitiesToUserFeed($activities, User $user) {

        foreach($activities as $activity) {
            $this->pushActivityToFeed($activity, $user);
        }

    }

    public function pushActivityToFeed(Activity $activity, User $user) {
        $score = strtotime(Timer::returnTime());
        $this->redis->zadd(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$user->getUserId(), array(
            $activity->getActivityId() => $score
        ));
        $this->redis->zremrangebyrank(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$user->getUserId(), 0, -301);
    }

}