<?php
namespace App\Handlers\Activity;
use App\MateyModels\ActivityTypeManager;
use App\MateyModels\User;
use App\Services\NotificationService;

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
    }

    public function pushNotification($activity) {
        $message = $this->getNotificationMessage($activity);

        $user = $this->getRelativeUser($message);
        if(empty($user)) return;
        $tokens = $this->getGcmTokens($user);

        $notificationService = new NotificationService();
        $notificationService->push($tokens, $message);
    }

    public function getRelativeUser ($message) {
        $activityType = $message['data']['activity_type'];

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();

        // FOLLOW NOTIFICATION --------------------------------------------------
        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT)
            $user->setUserId($message['data']['source']['user_id']);
        // APPROVE NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::APPROVE_ACT)
            $user->setUserId($message['data']['source']['user_id']);
        // BOOST NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::BOOST_ACT)
            $user->setUserId($message['data']['source']['user_id']);
        // REPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REPLY_CREATE_ACT)
            $user->setUserId($message['data']['parent']['user_id']);
        // REREPLY NOTIFICATION --------------------------------------------------
        else if($activityType == \App\MateyModels\Activity::REREPLY_CREATE_ACT)
            $user->setUserId($message['data']['parent']['user_id']);

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

        $message['data']['activity_type'] = $activityType; // <----------------------- add
        $message['data']['parent_type'] = $parentType; // <----------------------- add
        $message['data']['source_type'] = $sourceType; // <----------------------- add

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userGenerated = $userManager->readModelOneBy(array(
            'user_id' => $activity->getUserId()
        ), null, array('user_id', 'first_name', 'last_name'));

        $message['data']['user_generated'] = $userGenerated->asArray(); // <----------------------- add

        if($activityType == \App\MateyModels\Activity::FOLLOW_ACT) {
            $user = $userManager->readModelOneBy(array(
                'user_id' => $activity->getSourceId()
            ), null, array('user_id', 'first_name', 'last_name'));
            $message['data']['source'] = $user->asArray(); // <----------------------- add
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

                $message['data']['source'] = $reply->asArray(); // <----------------------- add
                $message['data']['parent'] = $post->asArray(); // <----------------------- add
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

                $message['data']['source'] = $rereply->asArray(); // <----------------------- add
                $message['data']['parent'] = $reply->asArray(); // <----------------------- add
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
                $message['data']['parent'] = $group->asArray(); // <----------------------- add
            }

            $message['data']['source'] = $post->asArray(); // <----------------------- add
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

            $message['data']['parent'] = $post->asArray(); // <----------------------- add
            $message['data']['source'] = $reply->asArray(); // <----------------------- add
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

            $message['data']['parent'] = $reply->asArray(); // <----------------------- add
            $message['data']['source'] = $rereply->asArray(); // <----------------------- add
        }

        return $message;

    }
}