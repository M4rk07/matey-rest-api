<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 19.10.16.
 * Time: 16.02
 */

namespace App\Services;


class NewsFeedGuruService extends BaseService
{

    public function pushToNewsFeeds($activity_id, $user_id, $check_id = null) {

        $followerManager = new FollowerService();
        $followers = $followerManager->returnFollowers($user_id);
        $followers[]['from_user'] = $user_id;

        foreach($followers as $follower) {
            if($check_id != null) {
                if ($this->redis->sismember(self::REDIS_POST_CHECKER_SET.$follower['from_user'], $check_id))
                    continue;
            }

            $this->redis->lpush(self::REDIS_NEWSFEED.$follower['from_user'], $activity_id);
            $this->redis->ltrim(self::REDIS_NEWSFEED.$follower['from_user'], 0, 500);
        }

    }

    public function getStatistics($type, $id) {

        return $this->redis->hgetall($type.":statistics:" . $id);

    }

}