<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 20.14
 */

namespace App\Handlers\Bulletin\Rereply;


use App\MateyModels\Activity;
use App\Services\PaginationService;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class RereplyHandler extends AbstractRereplyHandler
{

    public function createRereply (Application $app, Request $request, $replyId) {
        // Get user id based on token
        $userId = $request->request->get('user_id');

        $this->validateValue($replyId, [
            new NotBlank(),
            new UnsignedInteger()
        ]);

        // Getting json data in relation to Content-Type
        $contentType = $request->headers->get('Content-Type');

        $this->validateNumOfFiles($request);
        $jsonDataRequest = $this->getJsonPostData($request, $contentType);

        $jsonData = array();
        $jsonData['text'] = $this->gValidateText($jsonDataRequest);

        // Creating necessary data managers.
        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
        $activityManager = $this->modelManagerFactory->getModelManager('activity');
        $rereply = $rereplyManager->getModel();
        $activity = $activityManager->getModel();

        // Creating a Post model
        $rereply->setReplyId($replyId)
            ->setText($jsonData['text'])
            ->setUserId($userId);

        // Starting transaction
        $rereplyManager->startTransaction();
        try {
            // Writing Post model to database
            $rereply = $rereplyManager->createModel($rereply);

            $this->createActivity($rereply->getRereplyId(), $userId, $replyId, Activity::REPLY_TYPE, Activity::REREPLY_TYPE);

            // Commiting transaction on success
            $rereplyManager->commitTransaction();
        } catch (\Exception $e) {
            // Rollback transaction on failure
            $rereplyManager->rollbackTransaction();
            throw new ServerErrorException();
        }

        $replyManager = $this->modelManagerFactory->getModelManager('reply');
        $reply = $replyManager->getModel();
        $reply->setReplyId($replyId);
        $replyManager->incrNumOfReplies($reply);

        $rereplies = $this->fetchRereplies($replyId, 1, 0);
        $users = $this->getRereplyOwners($rereplies, 1);
        $finalResult = $this->getRereplyJsonObjects($rereplies, $users);

        return new JsonResponse($finalResult, 200);
    }

    public function deleteRereply (Application $app, Request $request, $rereplyId) {
        // Get user id based on token
        $userId = $request->request->get('user_id');

        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
        $rereply = $rereplyManager->getModel();

        $rereply->setDeleted(1);

        $rereplyManager->updateModel($rereply, array(
            'rereply_id' => $rereplyId,
            'user_id' => $userId
        ));

        return new JsonResponse(null, 200);
    }

    public function getRereplies (Application $app, Request $request, $replyId) {

        $limit = $request->query->get('limit');
        $offset = $request->query->get('offset');

        $rereplies = $this->fetchRereplies($replyId, $limit, $offset);

        $users = $this->getRereplyOwners($rereplies, $limit);

        $finalResult = $this->getRereplyJsonObjects($rereplies, $users);

        $paginationService = new PaginationService($finalResult, $limit, $offset,
            '/replies/'.$replyId.'/rereplies');

        return new JsonResponse($paginationService->getResponse(), 200);

    }

    public function getRereplyJsonObjects ($rereplies, $users) {
        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');

        $finalResult = array();
        foreach($rereplies as $rereply) {
            $arr = $rereply->asArray(array_diff($rereplyManager->getAllFields(), array('user_id')));
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

    public function fetchRereplies($replyId, $limit, $offset) {
        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
        return $rereplyManager->readModelBy(array(
            'reply_id' => $replyId,
            'deleted' => 0
        ), array('time_c' => 'DESC'), $limit, $offset);
    }

    public function getRereplyOwners($rereplies, $limit) {
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userIds = array();
        foreach ($rereplies as $rereply) {
            $userIds[] = $rereply->getUserId();
        }

        return $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $limit, null, array('user_id', 'first_name', 'last_name'));
    }

}