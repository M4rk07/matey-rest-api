<?php
namespace App\Handlers\Bulletin\Reply\StandardReply;
use App\Constants\Defaults\DefaultNumbers;
use App\Handlers\AbstractHandler;
use App\Handlers\Bulletin\Reply\AbstractReplyHandler;
use App\Handlers\Bulletin\Reply\StandardReplyHandlerInterface;
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

    public function mergeRepliesAndUsers ($replies, $users) {
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
                $arr['attachs'] = $reply->getAttachsLocation($reply->getAttachsNum());
            if ($reply->getLocationsNum() > 0)
                $arr['locations'] = $this->getLocations($reply->getReplyId(), Activity::REPLY_TYPE, $reply->getLocationsNum());

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

    public function getReplies($criteria, $count = DefaultNumbers::REPLIES_LIMIT) {

        $replyManager = $this->modelManagerFactory->getModelManager('reply');
        $replies = $replyManager->readModelBy($criteria, array('reply_id' => 'DESC'), $count);

        $userIds = array();
        foreach($replies as $reply) {
            $userIds[] = $reply->getUserId();
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $count, null, array('user_id', 'first_name', 'last_name'));

        return $this->mergeRepliesAndUsers($replies, $users);

    }

}