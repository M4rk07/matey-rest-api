<?php

namespace App\Handlers\Bulletin\StandardReply;
use App\Constants\Defaults\DefaultNumbers;
use App\Handlers\Bulletin\Reply\StandardReply\AbstractStandardReplyHandler;
use App\Handlers\File\PostAttachmentHandler;
use App\MateyModels\Activity;
use App\Services\PaginationService;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 17.44
 */
class StandardReplyHandler extends AbstractStandardReplyHandler
{

    public function handleCreateReply(Application $app, Request $request, $postId) {
        // Get user id based on token
        $userId = self::getTokenUserId($request);

        $this->validateValue($postId, [
            new NotBlank(),
            new UnsignedInteger()
        ]);

        // Getting json data in relation to Content-Type
        $contentType = $request->headers->get('Content-Type');

        $this->validateNumOfFiles($request);
        $jsonDataRequest = $this->getJsonPostData($request, $contentType);

        $jsonData = array();
        $jsonData['text'] = $this->gValidateText($jsonDataRequest, true);
        $jsonData['locations'] = $this->gValidateLocations($jsonDataRequest);

        // Creating necessary data managers.
        $replyManager = $this->modelManagerFactory->getModelManager('reply');
        $reply = $replyManager->getModel();

        // Creating a Post model
        $reply->setPostId($postId)
            ->setText($jsonData['text'])
            ->setAttachsNum($request->files->count())
            ->setLocationsNum(count($jsonData['locations']))
            ->setUserId($userId);

        // Starting transaction
        $replyManager->startTransaction();
        try {
            // Writing Post model to database
            $reply = $replyManager->createModel($reply);
            $this->createActivity($userId, $reply->getReplyId(), Activity::REPLY_TYPE, $postId, Activity::POST_TYPE, Activity::REPLY_CREATE_ACT);
            if($reply->getLocationsNum() > 0) {
                $this->insertLocations($jsonData['locations'], $reply->getReplyId(), Activity::REPLY_TYPE);
            }

            // Commiting transaction on success
            $replyManager->commitTransaction();
        } catch (\Exception $e) {
            // Rollback transaction on failure
            $replyManager->rollbackTransaction();
            throw new ServerErrorException();
        }

        // Calling the service for uploading Post attachments to S3 storage
        if(strpos($contentType, 'multipart/form-data') === 0) {
            $app['matey.file_handler.factory']->getFileHandler('post_attachment')->upload($app, $request, $reply->getReplyId(), PostAttachmentHandler::LOCATION_REPLIES);
        }

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();
        $post->setPostId($postId);
        $postManager->incrNumOfReplies($post);

        $replyResult = $this->getReplies(array(
            'reply_id' => $reply->getReplyId()
        ), $userId, 1);
        $finalResult['data'] = $replyResult[0];

        return new JsonResponse($finalResult, 200);
    }

    public function handleDeleteReply (Application $app, Request $request, $replyId) {
        // Get user id based on token
        $userId = self::getTokenUserId($request);

        $replyManager = $this->modelManagerFactory->getModelManager('reply');
        $reply = $replyManager->getModel();

        $reply->setDeleted(1);

        $replyManager->updateModel($reply, array(
            'reply_id' => $replyId,
            'user_id' => $userId
        ));

        return new JsonResponse(null, 200);
    }

    public function handleGetReplies(Application $app, Request $request, $postId) {
        $userId = self::getTokenUserId($request);

        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::REREPLIES_LIMIT
        ));

        $criteria['post_id'] = $postId;
        if(!empty($pagParams['max_id'])) $criteria['reply_id:<'] = $pagParams['max_id'];

        $finalResult = $this->getReplies($criteria, $userId, $pagParams['count']);

        $paginationService = new PaginationService($finalResult, $pagParams['count'],
            '/posts/'.$postId.'/replies', 'reply_id');

        return new JsonResponse($paginationService->getResponse(), 200);
    }



}