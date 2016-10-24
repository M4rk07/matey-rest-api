<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.10.16.
 * Time: 16.16
 */

namespace App\Controllers;

use App\Services\BaseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class NewsFeedController extends AbstractController
{

    public function getNewsFeedAction(Request $request) {

        $user_id = $request->get("user_id");
        $start = $request->get("start");
        $count = $request->get("count");

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($start, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid start.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($count, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid count.',
                'type' => 'numeric'
            ))
        ]);

        $activities_ids = $this->redisService->getIDsFromNewsFeed($user_id, $start, $count);
        if(empty($activities_ids)) return $this->returnOk();

        $activities = $this->service->getActivities($activities_ids, count($activities_ids));
        $finalActivities = array();

        foreach($activities as $activity) {
            $finalActivities[] = $this->handleActivity($activity);
        }

        return $this->returnOk($finalActivities);

    }

    public function handleActivity ($activity) {

        $activity['data'] = unserialize($activity['srl_data']);
        unset($activity['srl_data']);

        if($activity['activity_type'] == BaseService::TYPE_POST) {

            $activity['data']['statistics'] = $this->redisService->getPostStatistics($activity['source_id']);
            $users_respond = $this->redisService->getLastUsersRespond($activity['source_id']);
            $activity['data']['last_users_respond'] = array();
            foreach($users_respond as $user_id) {
                $activity['data']['last_users_respond'][] = $this->service->getUserRespondDataForActivity($user_id);
            }

        } else if ($activity['activity_type'] == BaseService::TYPE_RESPONSE) {

            $activity['data']['statistics'] = $this->redisService->getResponseStatistics($activity['source_id']);

        }

        return $activity;

    }

    public function shareActivityAction(Request $request) {

        $user_id = $request->request->get('user_id');
        $activity_id = $request->request->get('activity_id');

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($activity_id, [
            new NotBlank()
        ]);

        $activity = $this->service->getActivityOne($activity_id);
        if(empty($activity)) $this->returnNotOk("Requested resource doesn't exist.");
        $this->service->createActivity($user_id, $activity['source_id'], BaseService::TYPE_SHARE, $activity['parent_id'], $activity['parent_type'], $activity['srl_data']);

        $this->redisService->pushToNewsFeeds($activity_id, $user_id);

        $this->returnOk();

    }

}