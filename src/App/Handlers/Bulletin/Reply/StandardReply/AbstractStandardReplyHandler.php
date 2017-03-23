<?php
namespace App\Handlers\Bulletin\Reply\StandardReply;
use App\Constants\Defaults\DefaultNumbers;
use App\Handlers\AbstractHandler;
use App\Handlers\Bulletin\Reply\AbstractReplyHandler;
use App\Handlers\Bulletin\Reply\StandardReplyHandlerInterface;
use App\Handlers\File\PostAttachmentHandler;
use App\Handlers\Post\AbstractBulletinHandler;
use App\MateyModels\Activity;


/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 17.51
 */
abstract class AbstractStandardReplyHandler extends AbstractReplyHandler implements StandardReplyHandlerInterface
{

    public function mergeRepliesAndUsers ($replies, $users, $approvedIds) {
        $replyManager = $this->modelManagerFactory->getModelManager('reply');

        if(!is_array($replies)) $replies = array($replies);
        if(!is_array($users)) $users = array($users);

        $finalResult = array();
        foreach($replies as $reply) {
            $arr = $reply->asArray(array_diff($replyManager->getAllFields(), array('user_id', 'deleted')));
            foreach($users as $user) {
                if($user->getUserId() == $reply->getUserId()) {
                    $arr['user'] = $user->asArray();
                    break;
                }
            }
            if ($reply->getAttachsNum() > 0)
                $arr['attachs'] = PostAttachmentHandler::getAttachUrls($reply, PostAttachmentHandler::LOCATION_REPLIES);
            if ($reply->getLocationsNum() > 0)
                $arr['locations'] = $this->getLocations($reply->getReplyId(), Activity::REPLY_TYPE, $reply->getLocationsNum());

            if(in_array($reply->getReplyId(), $approvedIds)) $arr['approved'] = true;
            else $arr['approved'] = false;

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

    public function getReplies($criteria, $userRequestingId, $count = DefaultNumbers::REPLIES_LIMIT) {

        $replyManager = $this->modelManagerFactory->getModelManager('reply');
        $replies = $replyManager->readModelBy($criteria, array('reply_id' => 'DESC'), $count);

        if(empty($replies)) return array();

        $userIds = array();
        $replyIds = array();
        foreach($replies as $reply) {
            $userIds[] = $reply->getUserId();
            $replyIds[] = $reply->getReplyId();
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $count, null, array('user_id', 'first_name', 'last_name'));

        $approveManager = $this->modelManagerFactory->getModelManager('approve');
        $approves = $approveManager->readModelBy(array(
            'user_id' => $userRequestingId,
            'parent_id' => array_unique($replyIds),
            'parent_type' => Activity::REPLY_TYPE
        ), null, $count, null);

        $approvedIds = array();
        foreach($approves as $approve) {
            $approvedIds[] = $approve->getParentId();
        }

        return $this->mergeRepliesAndUsers($replies, $users, $approvedIds);

    }

}