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
            ->setParentId($parentId)
            ->setTimeC(new \DateTime());

        // Writing Activity model to database
        $activityManager->createModel($activity);

        if( in_array($activity->getActivityType(), array(
            \App\MateyModels\Activity::REREPLY_CREATE_ACT,
            \App\MateyModels\Activity::REPLY_CREATE_ACT)) )
            $this->pushNotification($activity);

    }

    public function pushNotification($activity) {
        $activityData = $this->getActivityData($activity);
        $userIds = $this->getNotificationRelativeUsers($activityData);

        if(empty($userIds)) return;
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $notificationData['tokens'] = array();
        foreach($userIds as $userId) {
            $tokens = $this->getGcmTokens($userId);
            if(empty($tokens)) continue;
            $notificationData['tokens'] = array_merge($notificationData['tokens'], $tokens);
            $userManager->pushNotification($userId, $activity->getActivityId());
        }

        $notificationData['data'] = $activityData;

        $notificationService = new NotificationService();
        $notificationService->push($notificationData);
    }

    public function getGcmTokens ($userId) {

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $deviceManager = $this->modelManagerFactory->getModelManager('device');
        $tokens = array();

        $deviceIds = $userManager->getLoggedDevices($userId);
        if( empty($deviceIds)) return;
        $devices = $deviceManager->readModelBy(array(
            'device_id' => $deviceIds
        ), null, count($deviceIds), null, array('gcm'));

        foreach($devices as $device) {
            $tokens[] = $device->getGcm();
        }

        return $tokens;
    }

    // ON PUSH
    public function getNotificationRelativeUsers ($activityData) {
        $activityType = $activityData['activity_type'];
        $userGeneratedId = $activityData['user']['user_id'];

        $userIds = array();

        // FOLLOW NOTIFICATION --------------------------------------------------
        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT
            && $userGeneratedId != $activityData['user_followed']['user_id'])
            $userIds[] = $activityData['user_followed']['user_id'];
        // APPROVE NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::APPROVE_ACT) {
            // APPROVE REPLY --------------------------------------------------
            if(isset($message['rereply'])
                && $userGeneratedId != $activityData['reply']['user']['user_id']) {
                $userIds[] = $activityData['reply']['user']['user_id'];
            }
            // APPROVE REREPLY --------------------------------------------------
            else if(!isset($message['rereply'])
                && $userGeneratedId != $activityData['post']['user']['user_id'])
                $userIds[] = $activityData['post']['user']['user_id'];
        }
        // BOOST NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::BOOST_ACT
            && $userGeneratedId != $activityData['post']['user']['user_id'])
            $userIds[] = $activityData['post']['user']['user_id'];
        // REPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REPLY_CREATE_ACT
            && $userGeneratedId != $activityData['post']['user']['user_id'])
            $userIds[] = $activityData['post']['user']['user_id'];
        // REREPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REREPLY_CREATE_ACT
            && $userGeneratedId != $activityData['reply']['user']['user_id'])
            $userIds[] = $activityData['reply']['user']['user_id'];

        return $userIds;
    }

    public function getActivityData ($activity) {
        $activityType = $activity->getActivityType();
        $parentType = $activity->getParentType();
        $sourceType = $activity->getSourceType();

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userGenerated = $userManager->readModelOneBy(array(
            'user_id' => $activity->getUserId()
        ), null, array('user_id', 'first_name', 'last_name', 'picture_url', 'is_silhouette'));

        $activityData['activity_id'] = $activity->getActivityId();
        $activityData['activity_type'] = $activityType;
        $activityData['source_type'] = $sourceType;
        $activityData['parent_type'] = $parentType;
        $activityData['user'] = $userGenerated->asArray(array('user_id', 'first_name', 'last_name', 'picture_url'));
        $activityData['time_c'] = $activity->getTimeC()->format(DefaultDates::DATE_FORMAT);

        $userManager = $this->modelManagerFactory->getModelManager('user');

        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT) {
            $user = $userManager->readModelOneBy(array(
                'user_id' => $activity->getSourceId()
            ), null, array('user_id', 'first_name', 'last_name', 'picture_url'));
            $activityData['user_followed'] = $user->asArray();
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
                $replyOwner = $userManager->readModelOneBy(array(
                    'user_id' => $reply->getUserId()
                ), null, array('user_id', 'first_name', 'last_name'));
                $postOwner = $userManager->readModelOneBy(array(
                    'user_id' => $post->getUserId()
                ), null, array('user_id', 'first_name', 'last_name'));

                $activityData['post']['post_id'] = $post->getPostId();
                $activityData['post']['title'] = $post->getTitle();
                $activityData['post']['user'] = $postOwner->asArray();
                $activityData['reply']['reply_id'] = $reply->getReplyId();
                $activityData['reply']['text'] = $reply->getText();
                $activityData['reply']['user'] = $replyOwner->asArray();
            }
            // APPROVE REREPLY --------------------------------------------------
            else if($sourceType == \App\MateyModels\Activity::REREPLY_TYPE) {

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
                $rereplyOwner = $userManager->readModelOneBy(array(
                    'user_id' => $rereply->getUserId()
                ), null, array('user_id', 'first_name', 'last_name'));
                $replyOwner = $userManager->readModelOneBy(array(
                    'user_id' => $reply->getUserId()
                ), null, array('user_id', 'first_name', 'last_name'));
                $postOwner = $userManager->readModelOneBy(array(
                    'user_id' => $post->getUserId()
                ), null, array('user_id', 'first_name', 'last_name'));

                $activityData['rereply']['rereply_id'] = $rereply->getRereplyId(); // <----------------------- add
                $activityData['rereply']['text'] = $rereply->getText();
                $activityData['rereply']['user_id'] = $rereplyOwner->asArray();
                $activityData['post']['post_id'] = $post->getPostId();
                $activityData['post']['title'] = $post->getTitle();
                $activityData['post']['user'] = $postOwner->asArray();
                $activityData['reply']['reply_id'] = $reply->getReplyId();
                $activityData['reply']['text'] = $reply->getText();
                $activityData['reply']['user'] = $replyOwner->asArray();
            }
        }
        // BOOST NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::BOOST_ACT) {
            $postManager = $this->modelManagerFactory->getModelManager('post');
            $post = $postManager->readModelOneBy(array(
                'post_id' => $activity->getSourceId()
            ), null, array('post_id', 'user_id', 'title'));
            $postOwner = $userManager->readModelOneBy(array(
                'user_id' => $post->getUserId()
            ), null, array('user_id', 'first_name', 'last_name'));
            if($activity->getParentId() != null) {
                $groupManager = $this->modelManagerFactory->getModelManager('group');
                $group = $groupManager->readModelOneBy(array(
                    'group_id' => $activity->getParentId()
                ), null, array('group_id', 'user_id', 'group_name'));
                $activityData['group'] = $group->asArray(); // <----------------------- add
            }

            $activityData['post']['post_id'] = $post->getPostId();
            $activityData['post']['title'] = $post->getTitle();
            $activityData['post']['user'] = $postOwner->asArray();
        }
        // REPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REPLY_CREATE_ACT) {
            $postManager = $this->modelManagerFactory->getModelManager('post');
            $post = $postManager->readModelOneBy(array(
                'post_id' => $activity->getParentId()
            ), null, array('post_id', 'user_id', 'title'));
            $postOwner = $userManager->readModelOneBy(array(
                'user_id' => $post->getUserId()
            ), null, array('user_id', 'first_name', 'last_name'));
            $replyManager = $this->modelManagerFactory->getModelManager('reply');
            $reply = $replyManager->readModelOneBy(array(
                'reply_id' => $activity->getSourceId()
            ), null, array('reply_id', 'user_id', 'text'));
            $replyOwner = $userManager->readModelOneBy(array(
                'user_id' => $reply->getUserId()
            ), null, array('user_id', 'first_name', 'last_name'));

            $activityData['post']['post_id'] = $post->getPostId();
            $activityData['post']['title'] = $post->getTitle();
            $activityData['post']['user'] = $postOwner->asArray();
            $activityData['reply']['reply_id'] = $reply->getReplyId();
            $activityData['reply']['text'] = $reply->getText();
            $activityData['reply']['user'] = $replyOwner->asArray();
        }
        // REREPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REREPLY_CREATE_ACT) {
            $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
            $rereply = $rereplyManager->readModelOneBy(array(
                'rereply_id' => $activity->getSourceId()
            ), null, array('rereply_id', 'user_id', 'text'));
            $replyManager = $this->modelManagerFactory->getModelManager('reply');
            $reply = $replyManager->readModelOneBy(array(
                'reply_id' => $activity->getParentId()
            ), null, array('reply_id', 'post_id', 'user_id', 'title'));
            $postManager = $this->modelManagerFactory->getModelManager('post');
            $post = $postManager->readModelOneBy(array(
                'post_id' => $reply->getPostId()
            ), null, array('post_id', 'user_id', 'title'));
            $rereplyOwner = $userManager->readModelOneBy(array(
                'user_id' => $rereply->getUserId()
            ), null, array('user_id', 'first_name', 'last_name'));
            $replyOwner = $userManager->readModelOneBy(array(
                'user_id' => $reply->getUserId()
            ), null, array('user_id', 'first_name', 'last_name'));
            $postOwner = $userManager->readModelOneBy(array(
                'user_id' => $post->getUserId()
            ), null, array('user_id', 'first_name', 'last_name'));

            $activityData['rereply']['rereply_id'] = $rereply->getRereplyId(); // <----------------------- add
            $activityData['rereply']['text'] = $rereply->getText();
            $activityData['rereply']['user_id'] = $rereplyOwner->asArray();
            $activityData['post']['post_id'] = $post->getPostId();
            $activityData['post']['title'] = $post->getTitle();
            $activityData['post']['user'] = $postOwner->asArray();
            $activityData['reply']['reply_id'] = $reply->getReplyId();
            $activityData['reply']['text'] = $reply->getText();
            $activityData['reply']['user'] = $replyOwner->asArray();
        }
        // REREPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::POST_CREATE_ACT) {
            $postManager = $this->modelManagerFactory->getModelManager('post');
            $post = $postManager->readModelOneBy(array(
                'post_id' => $activity->getSourceId()
            ), null, array('post_id', 'user_id', 'title'));
            $postOwner = $userManager->readModelOneBy(array(
                'user_id' => $post->getUserId()
            ), null, array('user_id', 'first_name', 'last_name'));

            $activityData['post']['post_id'] = $post->getPostId();
            $activityData['post']['title'] = $post->getTitle();
            $activityData['post']['user'] = $postOwner->asArray();
        }
        // GROUP NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::GROUP_CREATE_ACT) {
            $groupManager = $this->modelManagerFactory->getModelManager('group');
            $group = $groupManager->readModelOneBy(array(
                'group_id' => $activity->getSourceId()
            ), null, array('group_id', 'user_id', 'group_name'));
            $groupOwner = $userManager->readModelOneBy(array(
                'user_id' => $group->getGroupId()
            ), null, array('user_id', 'first_name', 'last_name'));

            $activityData['group']['group_id'] = $group->getGroupId();
            $activityData['group']['group_name'] = $group->getGroupName();
            $activityData['group']['user'] = $groupOwner->asArray();
        }

        return $activityData;

    }

    public function getNotifications (Request $request) {
        $userId = AbstractHandler::getTokenUserId($request);
        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::NOTIFICATIONS_LIMIT
        ));

        $finalResult = array();

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();
        $user->setUserId($userId);

        $allNotificationIds = $userManager->getNotifications($userId);

        $maxIdKey = 0;
        if(!empty($pagParams['max_id'])) {
            $index = array_search($pagParams['max_id'], $allNotificationIds);
            if($index !== false) {
                $maxIdKey = (int)$index + 1;
            } else {
                $paginationService = new PaginationService($finalResult, $pagParams['count'],
                    '/notifications', 'activity_id');

                return new JsonResponse($paginationService->getResponse(), 200);
            }
        }

        $notificationIds = array();
        for($i = $maxIdKey; $i < $maxIdKey + $pagParams['count']; $i++) {
            if(!isset($allNotificationIds[$i])) break;
            $notificationIds[] = $allNotificationIds[$i];
        }

        if(!empty($notificationIds)) {
            $activityManager = $this->modelManagerFactory->getModelManager('activity');

            $activities = $activityManager->readModelBy(array(
                'activity_id' => $notificationIds
            ), array('activity_id' => 'DESC'), $pagParams['count']);

            foreach ($activities as $activity) {
                $activityData = $this->getActivityData($activity);
                $finalResult[] = $activityData;
            }
        }

        $paginationService = new PaginationService($finalResult, $pagParams['count'],
            '/notifications', 'activity_id');

        return new JsonResponse($paginationService->getResponse(), 200);

    }

    public function getActivities (Request $request, $reqUserId) {
        $userId = self::getTokenUserId($request);

        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::NOTIFICATIONS_LIMIT
        ));

        $criteria['user_id'] = $reqUserId;
        if(!empty($pagParams['max_id'])) $criteria['activity_id:<'] = $pagParams['max_id'];
        $criteria['activity_type'] = array(
            \App\MateyModels\Activity::GROUP_CREATE_ACT,
            \App\MateyModels\Activity::POST_CREATE_ACT,
            \App\MateyModels\Activity::REPLY_CREATE_ACT,
            \App\MateyModels\Activity::REREPLY_CREATE_ACT
        );

        $activityManager = $this->modelManagerFactory->getModelManager('activity');
        $activities = $activityManager->readModelBy($criteria, array('activity_id' => 'DESC'), $pagParams['count']);

        $finalResult = array();
        if(!empty($activities)) {
            foreach ($activities as $activity) {
                $activityData = $this->getActivityData($activity);
                $finalResult[] = $activityData;
            }
        }

        $paginationService = new PaginationService($finalResult, $pagParams['count'],
            '/users/'.$reqUserId.'/activities', 'activity_id');

        return new JsonResponse($paginationService->getResponse(), 200);
    }
}