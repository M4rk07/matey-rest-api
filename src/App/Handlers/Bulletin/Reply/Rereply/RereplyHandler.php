<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 20.14
 */

namespace App\Handlers\Bulletin\Rereply;


use App\Constants\Defaults\DefaultNumbers;
use App\MateyModels\Activity;
use App\Services\PaginationService;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class RereplyHandler extends AbstractRereplyHandler
{

    public function handleCreateRereply (Application $app, Request $request, $replyId) {
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
        $jsonData['text'] = $this->gValidateText($jsonDataRequest, true);

        // Creating necessary data managers.
        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');
        $rereply = $rereplyManager->getModel();

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

        $rereplyResult = $this->getRereplies(array(
            'rereply_id' => $rereply->getRereplyId()
        ), $userId, 1);
        $finalResult['data'] = $rereplyResult[0];

        return new JsonResponse($finalResult, 200);
    }

    public function handleDeleteRereply (Application $app, Request $request, $rereplyId) {
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

    public function handleGetRereplies (Application $app, Request $request, $replyId) {

        $userId = $request->request->get('user_id');

        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::REREPLIES_LIMIT
        ));

        $criteria['reply_id'] = $replyId;
        if(!empty($pagParams['max_id'])) $criteria['rereply_id:<'] = $pagParams['max_id'];

        $finalResult = $this->getRereplies($criteria, $userId, $pagParams['count']);

        $paginationService = new PaginationService($finalResult, $pagParams['count'],
            '/replies/'.$replyId.'/rereplies', 'rereply_id');

        return new JsonResponse($paginationService->getResponse(), 200);

    }



}