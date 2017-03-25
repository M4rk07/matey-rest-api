<?php
namespace App\Handlers\Activity;
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
        //$notificationService = new NotificationService();
        //$notificationService->push($tokens, $msg);
    }

    public function getRelativeUser ($message) {
        $activityType = $message['activity_type'];

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();

        // FOLLOW NOTIFICATION --------------------------------------------------
        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT)
            $user->setUserId($message['source']['user_id']);
        // APPROVE NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::APPROVE_ACT)
            $user->setUserId($message['source']['user_id']);
        // BOOST NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::BOOST_ACT)
            $user->setUserId($message['source']['user_id']);
        // REPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REPLY_CREATE_ACT)
            $user->setUserId($message['parent']['user_id']);
        // REREPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REREPLY_CREATE_ACT)
            $user->setUserId($message['parent']['user_id']);

        return $user;
    }

    public function getGcmTokens ($users) {
        if(!is_array($users)) $users = array($users);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $deviceManager = $this->modelManagerFactory->getModelManager('device');
        $tokens = array();

        foreach($users as $user) {
            $deviceIds = $userManager->getLoggedDevices($user);
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
        $message['activity_type'] = $activityType; // <----------------------- add
        $message['parent_type'] = $parentType; // <----------------------- add
        $message['source_type'] = $sourceType; // <----------------------- add

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userGenerated = $userManager->readModelOneBy(array(
            'user_id' => $activity->getUserId()
        ), null, array('user_id', 'first_name', 'last_name'));

        $message['user_generated'] = $userGenerated->asArray(); // <----------------------- add

        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT) {
            $user = $userManager->readModelOneBy(array(
                'user_id' => $activity->getSourceId()
            ), null, array('user_id', 'first_name', 'last_name'));
            $message['source'] = $user->asArray(); // <----------------------- add
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

                $message['source'] = $reply->asArray(); // <----------------------- add
                $message['parent'] = $post->asArray(); // <----------------------- add
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

                $message['source'] = $rereply->asArray(); // <----------------------- add
                $message['parent'] = $reply->asArray(); // <----------------------- add
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
                $message['parent'] = $group->asArray(); // <----------------------- add
            }

            $message['source'] = $post->asArray(); // <----------------------- add
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

            $message['parent'] = $post->asArray(); // <----------------------- add
            $message['source'] = $reply->asArray(); // <----------------------- add
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

            $message['parent'] = $reply->asArray(); // <----------------------- add
            $message['source'] = $rereply->asArray(); // <----------------------- add
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