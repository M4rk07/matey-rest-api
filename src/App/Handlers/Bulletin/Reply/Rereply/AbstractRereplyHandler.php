<?php

namespace App\Handlers\Bulletin\Rereply;
use App\Constants\Defaults\DefaultNumbers;
use App\Handlers\Bulletin\Reply\AbstractReplyHandler;
use App\Handlers\Post\AbstractBulletinHandler;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 20.11
 */
abstract class AbstractRereplyHandler extends AbstractReplyHandler implements RereplyHandlerInterface
{
    public function mergeRerepliesAndUsers ($rereplies, $users) {
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

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

    public function getRereplies($criteria, $limit = DefaultNumbers::REREPLIES_LIMIT, $offset = 0) {
        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
        $rereplies = $rereplyManager->readModelBy($criteria, array('time_c' => 'DESC'), $limit, $offset);

        $userIds = array();
        foreach($rereplies as $rereply) {
            $userIds[] = $rereply->getUserId();
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $limit, $offset, array('user_id', 'first_name', 'last_name'));

        return $this->mergeRerepliesAndUsers($rereplies, $users);
    }
}