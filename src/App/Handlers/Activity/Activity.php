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

define('APPROVE_MESSAGE', '@1$s approved your reply on: "@2$s".');
define('BOOST_MESSAGE', '@1$s boosted your post: "@2$s".');
define('FOLLOW_MESSAGE', '@1$s is following you.');
define('POST_CREATE_MESSAGE', '@1$s posted: "@2$s".');
define('REREPLY_CREATE_MESSAGE', '@1$s replied on your reply: "@2$s."');
define('REPLY_CREATE_MESSAGE', '@1$s replied on your post: "@2$s."');
define('GROUP_CREATE_MESSAGE', '@1$s created group @2$s.');

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

        if( in_array($activity->getActivityType(), array(
            \App\MateyModels\Activity::REREPLY_CREATE_ACT,
            \App\MateyModels\Activity::REPLY_CREATE_ACT,
            \App\MateyModels\Activity::APPROVE_ACT,
            \App\MateyModels\Activity::BOOST_ACT,
            \App\MateyModels\Activity::FOLLOW_ACT)) )
            $this->pushNotification($activity);
    }

    public function pushNotification($activity) {
        $allData = array();

        $activityData = $this->getActivityData($activity);
        $userIds = $this->getNotificationRelativeUsers($activityData);

        if(empty($userIds)) return;
        $userManager = $this->modelManagerFactory->getModelManager('user');
        foreach($userIds as $userId) {
            $tokens = $this->getGcmTokens($userId);
            if(empty($tokens)) continue;
            $notificationData = $this->getNotificationData($activityData, $userId);
            $notificationData['tokens'] = $tokens;
            $allData[] = $notificationData;
            $userManager->pushNotification($userId, $activity->getActivityId());
        }

        $notificationService = new NotificationService();
        $notificationService->push($allData);
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

    public function mergeArgsAndTemplate($template, $args) {
        $i=1;
        foreach($args as $arg) {
            $template = str_replace('@'.$i++.'$s', $arg, $template);
        }
        return $template;
    }

    // ON PUSH
    public function getNotificationRelativeUsers ($activityData) {
        $activityType = $activityData['activity_type'];
        $userGeneratedId = $activityData['user_generated']['user_id'];

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

    public function getNotificationData ($activityData, $userReceiverId) {

        $activityType = $activityData['activity_type'];
        $parentType = $activityData['parent_type'];
        $sourceType = $activityData['source_type'];

        $notificationData['to'] = $userReceiverId;
        $notificationData['data']['activity_type'] = $activityType;
        $notificationData['data']['picture_url'] = $activityData['user_generated']['picture_url'];

        $args = array();
        $args[] = $activityData['user_generated']['first_name'] . " " . $activityData['user_generated']['last_name'];

        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT) {
            $notificationData['data']['message'] = $this->mergeArgsAndTemplate(FOLLOW_MESSAGE, $args);
            $notificationData['data']['user_following']['user_id'] = $activityData['user_generated']['user_id'];
        }
        // APPROVE NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::APPROVE_ACT) {
            // APPROVE REPLY --------------------------------------------------
            if($sourceType == \App\MateyModels\Activity::REPLY_TYPE) {
                $args[] = $activityData['post']['title'];
                $notificationData['data']['message'] = $this->mergeArgsAndTemplate(APPROVE_MESSAGE, $args);
                $notificationData['data']['reply']['reply_id'] = $activityData['reply']['reply_id'];
            }
            // APPROVE REREPLY --------------------------------------------------
            else if($parentType == \App\MateyModels\Activity::REREPLY_TYPE) {
                $args[] = $activityData['post']['title'];
                $notificationData['data']['message'] = $this->mergeArgsAndTemplate(APPROVE_MESSAGE, $args);
                $notificationData['data']['rereply']['rereply_id'] = $activityData['rereply']['rereply_id'];
            }
            $notificationData['data']['post']['post_id'] = $activityData['post']['post_id'];
        }
        // BOOST NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::BOOST_ACT) {
            $args[] = $activityData['post']['title'];
            $notificationData['data']['message'] = $this->mergeArgsAndTemplate(BOOST_MESSAGE, $args);
            $notificationData['data']['post']['post_id'] = $activityData['post']['post_id'];
        }
        // REPLY AND REREPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REPLY_CREATE_ACT) {
            $args[] = $activityData['post']['title'];
            $notificationData['data']['message'] = $this->mergeArgsAndTemplate(REPLY_CREATE_MESSAGE, $args);
            $notificationData['data']['post']['post_id'] = $activityData['post']['post_id'];
            $notificationData['data']['reply']['reply_id'] = $activityData['reply']['reply_id'];
        }
        else if($activityType == \App\MateyModels\Activity::REREPLY_CREATE_ACT) {
            $args[] = $activityData['reply']['text'];
            $notificationData['data']['message'] = $this->mergeArgsAndTemplate(REREPLY_CREATE_MESSAGE, $args);
            $notificationData['data']['post']['post_id'] = $activityData['post']['post_id'];
            $notificationData['data']['reply']['reply_id'] = $activityData['reply']['reply_id'];
            $notificationData['data']['rereply']['rereply_id'] = $activityData['reply']['reply_id'];
        }

        return $notificationData;
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
        $activityData['user_generated'] = $userGenerated->asArray(array('user_id', 'first_name', 'last_name', 'picture_url'));
        $activityData['time_c'] = $activity->getTimeC();

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
                    'user_id' => $reply->getReplyId()
                ), null, array('user_id', 'first_name', 'last_name'));
                $postOwner = $userManager->readModelOneBy(array(
                    'user_id' => $post->getPostId()
                ), null, array('user_id', 'first_name', 'last_name'));

                $activityData['post']['post_id'] = $post->getPostId();
                $activityData['post']['title'] = $post->getTitle();
                $activityData['post']['user'] = $postOwner->asArray();
                $activityData['reply']['reply_id'] = $reply->getReplyId();
                $activityData['reply']['text'] = $reply->getText();
                $activityData['reply']['user'] = $replyOwner->asArray();
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

        return $activityData;

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

        $allNotificationIds = $userManager->getNotifications($userId);

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
                $activityData = $this->getActivityData($activity);
                $notificationData = $this->getNotificationData($activityData, $userId);
                $finalResult[] = $notificationData['data'];
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
            \App\MateyModels\Activity::APPROVE_ACT,
            \App\MateyModels\Activity::GROUP_CREATE_ACT,
            \App\MateyModels\Activity::POST_CREATE_ACT,
            \App\MateyModels\Activity::REPLY_CREATE_ACT,
            \App\MateyModels\Activity::REREPLY_CREATE_ACT
        );

        $activityManager = $this->modelManagerFactory->getModelManager('activity');
        $activities = $activityManager->readModelBy($criteria, null, $pagParams['count']);

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