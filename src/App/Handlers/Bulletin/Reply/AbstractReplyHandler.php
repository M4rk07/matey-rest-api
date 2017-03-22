<?php
namespace App\Handlers\Bulletin\Reply;
use App\Handlers\Post\AbstractBulletinHandler;
use App\MateyModels\Activity;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.3.17.
 * Time: 16.40
 */
abstract class AbstractReplyHandler extends AbstractBulletinHandler implements ReplyHandlerInterface
{
    public function handleApprove(Application $app, Request $request, $type, $id) {
        $userId = $request->request->get('user_id');

        $approveManager = $this->modelManagerFactory->getModelManager('approve');
        $approve = $approveManager->getModel();
        $approve->setUserId($userId)
            ->setParentId($id)
            ->setParentType($type);
        $approveManager->createModel($approve);

        if($type == Activity::REPLY_TYPE) {
            $replyManager = $this->modelManagerFactory->getModelManager('reply');
        } else if($type == Activity::REREPLY_TYPE) {
            $replyManager = $this->modelManagerFactory->getModelManager('rereply');
        } else throw new ServerErrorException();

        $reply = $replyManager->getModel();
        $reply->setId($id);
        $replyManager->incrNumOfApproves($reply);

        if($type == Activity::REPLY_TYPE) {
            $reply = $replyManager->readModelOneBy(array(
                'reply_id' => $id
            ), null, array('reply_id', 'post_id'));
            $this->createActivity($userId, $reply->getId(), Activity::REPLY_TYPE, $reply->getPostId(), Activity::POST_TYPE, Activity::APPROVE_ACT);
        } else if($type == Activity::REREPLY_TYPE) {
            $reply = $replyManager->readModelOneBy(array(
                'rereply_id' => $id
            ), null, array('rereply_id', 'reply_id'));
            $this->createActivity($userId, $reply->getId(), Activity::REREPLY_TYPE, $reply->getReplyId(), Activity::REPLY_TYPE, Activity::APPROVE_ACT);
        } else throw new ServerErrorException();

        return new JsonResponse(null, 200);
    }
}