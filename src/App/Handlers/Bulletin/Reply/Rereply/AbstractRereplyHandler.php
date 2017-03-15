<?php

namespace App\Handlers\Bulletin\Rereply;
use App\Constants\Defaults\DefaultNumbers;
use App\Handlers\Bulletin\Reply\AbstractReplyHandler;
use App\Handlers\Post\AbstractBulletinHandler;
use App\MateyModels\Activity;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 20.11
 */
abstract class AbstractRereplyHandler extends AbstractReplyHandler implements RereplyHandlerInterface
{
    public function mergeRerepliesAndUsers ($rereplies, $users, $approvedIds) {
        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');

        if(!is_array($rereplies)) $rereplies = array($rereplies);
        if(!is_array($users)) $users = array($users);

        $finalResult = array();
        foreach($rereplies as $rereply) {
            $arr = $rereply->asArray(array_diff($rereplyManager->getAllFields(), array('user_id', 'deleted')));
            foreach($users as $user) {
                if($user->getUserId() == $rereply->getUserId()) {
                    $arr['user'] = $user->asArray();
                    break;
                }
            }

            if(in_array($rereply->getRereplyId(), $approvedIds)) $arr['approved'] = true;
            else $arr['approved'] = false;

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

    public function getRereplies($criteria, $userRequestingId, $count = DefaultNumbers::REREPLIES_LIMIT) {
        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
        $rereplies = $rereplyManager->readModelBy($criteria, array('rereply_id' => 'DESC'), $count);

        $userIds = array();
        $rereplyIds = array();
        foreach($rereplies as $rereply) {
            $userIds[] = $rereply->getUserId();
            $rereplyIds[] = $rereply->getRereplyId();
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $count, null, array('user_id', 'first_name', 'last_name'));

        $approveManager = $this->modelManagerFactory->getModelManager('approve');
        $approves = $approveManager->readModelBy(array(
            'user_id' => $userRequestingId,
            'parent_id' => array_unique($rereplyIds),
            'parent_type' => Activity::REREPLY_TYPE
        ), null, $count, null);

        $approvedIds = array();
        foreach($approves as $approve) {
            $approvedIds[] = $approve->getParentId();
        }

        return $this->mergeRerepliesAndUsers($rereplies, $users, $approvedIds);
    }
}