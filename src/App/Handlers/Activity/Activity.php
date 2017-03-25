<?php
namespace App\Handlers\Activity;
use App\Constants\Defaults\DefaultDates;
use App\Constants\Defaults\DefaultNumbers;
use App\Handlers\AbstractHandler;
use App\MateyModels\ActivityTypeManager;
use App\MateyModels\User;
use App\Services\NotificationService;
use App\Services\PaginationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.3.17.
 * Time: 16.53
 */
class Activity extends AbstractActivity
{
    public function createActivity($userId, $sourceId, $sourceType, $parentId, $parentType, $activityType) {
        $activityManager = $this->modelManagerFactory->getModelManager('activity');
        $activity = $activityManager->getModel();

        $activity->setSourceId($sourceId)
            ->setUserId($userId)
            ->setParentType($parentType)
            ->setSourceType($sourceType)
            ->setActivityType($activityType)
            ->setParentId($parentId);

        // Writing Activity model to database
        $activityManager->createModel($activity);
        $this->pushNotification($activity);

    }

    public function pushNotification($activity) {
        $message = $this->getNotificationMessage($activity);
        $user = $this->getRelativeUser($message);
        if(empty($user)) return;
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userManager->pushNotification($user, $activity);
        $tokens = $this->getGcmTokens($user);

        $msg['data'] = $message;
        $notificationService = new NotificationService();
        $notificationService->push($tokens, $msg);
    }

    public function getRelativeUser ($message) {
        $activityType = $message['activity_type'];

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();

        // FOLLOW NOTIFICATION --------------------------------------------------
        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT)
            $user->setUserId($message['user_followed']['user_id']);
        // APPROVE NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::APPROVE_ACT) {
            // APPROVE REPLY --------------------------------------------------
            if(isset($message['rereply'])) {
                $user->setUserId($message['reply']['user_id']);
            }
            // APPROVE REREPLY --------------------------------------------------
            else $user->setUserId($message['post']['user_id']);
        }
        // BOOST NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::BOOST_ACT)
            $user->setUserId($message['post']['user_id']);
        // REPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REPLY_CREATE_ACT)
            $user->setUserId($message['post']['user_id']);
        // REREPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REREPLY_CREATE_ACT)
            $user->setUserId($message['reply']['user_id']);

        return $user;
    }

    public function getGcmTokens ($users) {
        if(!is_array($users)) $users = array($users);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $deviceManager = $this->modelManagerFactory->getModelManager('device');
        $tokens = array();

        foreach($users as $user) {
            $deviceIds = $userManager->getLoggedDevices($user);
            if( empty($deviceIds)) continue;
            $devices = $deviceManager->readModelBy(array(
                'device_id' => $deviceIds
            ), null, count($deviceIds), null, array('gcm'));

            foreach($devices as $device) {
                $tokens[] = $device->getGcm();
            }
        }

        return $tokens;
    }

    public function getNotificationMessage ($activity) {
        $activityType = $activity->getActivityType();
        $parentType = $activity->getParentType();
        $sourceType = $activity->getSourceType();

        $message['activity_id'] = $activity->getActivityId();
        $message['activity_type'] = $activityType;
        $message['time_c'] = $activity->getTimeC();

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userGenerated = $userManager->readModelOneBy(array(
            'user_id' => $activity->getUserId()
        ), null, array('user_id', 'first_name', 'last_name', 'picture_url'));
        $message['picture_url'] = $userGenerated->getPictureUrl();

        $message['message'] = $userGenerated->getFirstName() . " " . $userGenerated->getLastName();

        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT) {
            $user = $userManager->readModelOneBy(array(
                'user_id' => $activity->getSourceId()
            ), null, array('user_id', 'first_name', 'last_name', 'picture_url'));
            $message['user']['user_id'] = $userGenerated->getUserId();
            $message['user_followed'] = $user->asArray();
            $message['message'] .= " is following you."; // <----------------------- add
        }
        // APPROVE NOTIFICATION --------------------------------------------------
        if($activityType == \App\MateyModels\Activity::APPROVE_ACT) {
            // APPROVE REPLY --------------------------------------------------
            if($sourceType == \App\MateyModels\Activity::REPLY_TYPE) {
                $replyManager = $this->modelManagerFactory->getModelManager('reply');
                $reply = $replyManager->readModelOneBy(array(
                    'reply_id' => $activity->getSourceId()
                ), null, array('reply_id', 'user_id', 'text'));
                $postManager = $this->modelManagerFactory->getModelManager('post');
                $post = $postManager->readModelOneBy(array(
                    'post_id' => $activity->getParentId()
                ), null, array('post_id', 'user_id', 'title'));
                $message['post'] = $post->asArray();
                $message['reply'] = $reply->asArray();
                $message['message'] .= " approved your reply."; // <----------------------- add
            }
            // APPROVE REREPLY --------------------------------------------------
            else if($parentType == \App\MateyModels\Activity::REREPLY_TYPE) {
                $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
                $rereply = $rereplyManager->readModelOneBy(array(
                    'rereply_id' => $activity->getSourceId()
                ), null, array('rereply_id', 'user_id', 'text'));
                $replyManager = $this->modelManagerFactory->getModelManager('reply');
                $reply = $replyManager->readModelOneBy(array(
                    'reply_id' => $activity->getParentId()
                ), null, array('reply_id', 'user_id', 'text'));
                $postManager = $this->modelManagerFactory->getModelManager('post');
                $post = $postManager->readModelOneBy(array(
                    'reply_id' => $reply->getReplyId()
                ), null, array('post_id', 'user_id', 'title'));

                $message['rereply'] = $rereply->asArray(); // <----------------------- add
                $message['reply'] = $reply->asArray();
                $message['post'] = $post->asArray();
                $message['message'] .= " approved your reply."; // <----------------------- add
            }
        }
        // BOOST NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::BOOST_ACT) {
            $postManager = $this->modelManagerFactory->getModelManager('post');
            $post = $postManager->readModelOneBy(array(
                'post_id' => $activity->getSourceId()
            ), null, array('post_id', 'user_id', 'title'));
            if($activity->getParentId() != null) {
                $groupManager = $this->modelManagerFactory->getModelManager('group');
                $group = $groupManager->readModelOneBy(array(
                    'group_id' => $activity->getParentId()
                ), null, array('group_id', 'user_id', 'group_name'));
                $message['group'] = $group->asArray(); // <----------------------- add
            }

            $message['post'] = $post->asArray(); // <----------------------- add
            $message['message'] .= " boosted your post.";
        }
        // REPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REPLY_CREATE_ACT) {
            $postManager = $this->modelManagerFactory->getModelManager('post');
            $post = $postManager->readModelOneBy(array(
                'post_id' => $activity->getParentId()
            ), null, array('post_id', 'user_id', 'title'));
            $replyManager = $this->modelManagerFactory->getModelManager('reply');
            $reply = $replyManager->readModelOneBy(array(
                'reply_id' => $activity->getSourceId()
            ), null, array('reply_id', 'user_id', 'text'));

            $message['post'] = $post->asArray(); // <----------------------- add
            $message['reply'] = $reply->asArray(); // <----------------------- add
            $message['message'] .= " replied on your post";
        }
        // REREPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REREPLY_CREATE_ACT) {
            $replyManager = $this->modelManagerFactory->getModelManager('reply');
            $reply = $replyManager->readModelOneBy(array(
                'reply_id' => $activityType->getParentId()
            ), null, 'reply_id', 'user_id', 'title');
            $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
            $rereply = $rereplyManager->readModelOneBy(array(
                'rereply_id' => $activity->getSourceId()
            ), null, array('rereply_id', 'user_id', 'text'));
            $postManager = $this->modelManagerFactory->getModelManager('post');
            $post = $postManager->readModelOneBy(array(
                'reply_id' => $reply->getReplyId()
            ), null, array('post_id', 'user_id', 'title'));

            $message['reply'] = $reply->asArray(); // <----------------------- add
            $message['rereply'] = $rereply->asArray(); // <----------------------- add
            $message['post'] = $post->asArray();
            $message['message'] .= " replied on your reply.";
        }

        return $message;

    }

    public function getNotifications (Request $request) {
        $userId = AbstractHandler::getTokenUserId($request);
        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::NOTIFICATIONS_LIMIT
        ));

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();
        $user->setUserId($userId);

        $allNotificationIds = $userManager->getNotifications($user);

        $maxIdKey = 0;
        if(!empty($pagParams['max_id'])) $maxIdKey = (int)(array_search($pagParams['max_id'], $allNotificationIds)) + 1;

        $notificationIds = array();
        for($i = $maxIdKey; $i < $maxIdKey + $pagParams['count']; $i++) {
            if(!isset($allNotificationIds[$i])) break;
            $notificationIds[] = $allNotificationIds[$i];
        }
        $finalResult = array();
        if(!empty($notificationIds)) {

            $activityManager = $this->modelManagerFactory->getModelManager('activity');

            $activities = $activityManager->readModelBy(array(
                'activity_id' => $notificationIds
            ), null, $pagParams['count']);

            foreach ($activities as $activity) {
                $finalResult[] = $this->getNotificationMessage($activity);
            }
        }

        $paginationService = new PaginationService($finalResult, $pagParams['count'],
            '/notifications', 'activity_id');

        return new JsonResponse($paginationService->getResponse(), 200);

    }
}