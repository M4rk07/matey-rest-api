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

        $rereplies = $this->fetchObjects(array(
            'rereply_id' => $rereply->getRereplyId()
        ), 1, 0, 'rereply');
        $users = $this->getObjectOwners($rereplies, 1);
        $finalResult = $this->getJsonObjects($rereplies, $users, 'rereply');

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

        $rereplies = $this->fetchObjects(array(
            'reply_id' => $replyId
        ), $limit, $offset, 'rereply');
        $users = $this->getObjectOwners($rereplies, $limit);
        $finalResult = $this->getJsonObjects($rereplies, $users, 'rereply');

        $paginationService = new PaginationService($finalResult, $limit, $offset,
            '/replies/'.$replyId.'/rereplies');

        return new JsonResponse($paginationService->getResponse(), 200);

    }

}