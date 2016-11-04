<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.13
 */

namespace App\MateyManagers;


use App\MateyModels\Activity;
use App\MateyModels\User;
use App\Services\BaseService;

class ActivityManager extends BaseService
{

    public function createActivity(Activity $activity) {

        $result = $this->db->executeUpdate("INSERT INTO ".self::T_ACTIVITY." (user_id, source_id, activity_type, parent_id, parent_type, srl_data) VALUES(?,?,?,?,?,?)",
            array($activity->getUserId(), $activity->getSourceId(),
                $activity->getActivityType(), $activity->getParentId(),
                $activity->getParentType(), $activity->getSrlData()));
        if($result<=0) return false;
        $activity->setActivityId($this->db->lastInsertId());
        return $activity;

    }

    public function pushToNewsFeeds (Activity $activity, User $user) {
        $followers = $user->getFollowers();
        $followers[] = $user;

        $newsfeedManager = new NewsfeedManager();

        foreach($followers as $follower) {
            $newsfeedManager->pushActivityToFeed($activity, $follower);
        }
    }

    public function deleteActivity(Activity $activity) {

        $result = $this->db->executeUpdate("UPDATE ".self::T_ACTIVITY." SET deleted = 1 WHERE source_id = ? AND activity_type = ?",
            array($activity->getSourceId(), $activity->getActivityType()));
        if($result<=0) return false;
        return $activity;

    }

}