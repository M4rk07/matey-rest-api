<?php

namespace App\Handlers\Bulletin\StandardReply;
use App\Handlers\Bulletin\Reply\StandardReply\AbstractStandardReplyHandler;
use App\MateyModels\Activity;
use App\Services\PaginationService;
use App\Validators\UnsignedInteger;
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

    public function createReply(Application $app, Request $request, $postId) {
        // Get user id based on token
        $userId = $request->request->get('user_id');

        $this->validateValue($postId, [
            new NotBlank(),
            new UnsignedInteger()
        ]);

        // Getting json data in relation to Content-Type
        $contentType = $request->headers->get('Content-Type');

        $this->validateNumOfFiles($request);
        $jsonDataRequest = $this->getJsonPostData($request, $contentType);

        $jsonData = array();
        $jsonData['text'] = $this->gValidateText($jsonDataRequest);
        $jsonData['locations'] = $this->gValidateLocations($jsonDataRequest);

        // Creating necessary data managers.
        $replyManager = $this->modelManagerFactory->getModelManager('reply');
        $activityManager = $this->modelManagerFactory->getModelManager('activity');
        $reply = $replyManager->getModel();
        $activity = $activityManager->getModel();

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

            $this->createActivity($reply->getReplyId(), $userId, $postId, Activity::POST_TYPE, Activity::REPLY_TYPE);

            // Commiting transaction on success
            $replyManager->commitTransaction();
        } catch (\Exception $e) {
            // Rollback transaction on failure
            $replyManager->rollbackTransaction();
            throw new ServerErrorException();
        }

        if($reply->getLocationsNum() > 0) {
            $locationManager = $this->modelManagerFactory->getModelManager('location');
            foreach($jsonData['locations'] as $location) {
                $newLocation = $locationManager->getModel();
                $newLocation->setParentId($reply->getReplyId())
                    ->setParentType(Activity::POST_TYPE)
                    ->setLatt($location->latt)
                    ->setLongt($location->longt);
                $locationManager->createModel($newLocation);
            }
        }

        // Calling the service for uploading Post attachments to S3 storage
        if(strpos($contentType, 'multipart/form-data') === 0) {
            $app['matey.file_handler.factory']->getFileHandler('post_attachment')->upload($app, $request, $reply->getReplyId());
        }

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();
        $post->setPostId($postId);
        $postManager->incrNumOfReplies($post);

        $replies = $this->fetchReplies($postId, 1, 0);
        $users = $this->getReplyOwners($replies, 1);
        $finalResult = $this->getReplyJsonObjects($replies, $users);

        return new JsonResponse($finalResult, 200);
    }

    public function deleteReply (Application $app, Request $request, $replyId) {
        // Get user id based on token
        $userId = $request->request->get('user_id');

        $replyManager = $this->modelManagerFactory->getModelManager('reply');
        $reply = $replyManager->getModel();

        $reply->setDeleted(1);

        $replyManager->updateModel($reply, array(
            'reply_id' => $replyId,
            'user_id' => $userId
        ));

        return new JsonResponse(null, 200);
    }

    public function getReplies(Application $app, Request $request, $postId) {
        $limit = $request->query->get('limit');
        $offset = $request->query->get('offset');

        $replies = $this->fetchReplies($postId, $limit, $offset);
        $users = $this->getReplyOwners($replies, $limit);
        $finalResult = $this->getReplyJsonObjects($replies, $users);

        $paginationService = new PaginationService($finalResult, $limit, $offset,
            '/posts/'.$postId.'/replies');

        return new JsonResponse($paginationService->getResponse(), 200);
    }

    public function fetchReplies($postId, $limit, $offset) {
        $replyManager = $this->modelManagerFactory->getModelManager('reply');
        return $replyManager->readModelBy(array(
            'post_id' => $postId,
            'deleted' => 0
        ), array('time_c' => 'DESC'), $limit, $offset);
    }

    public function getReplyOwners($replies, $limit) {
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userIds = array();
        foreach ($replies as $reply) {
            $userIds[] = $reply->getUserId();
        }

        return $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $limit, null, array('user_id', 'first_name', 'last_name'));
    }

    public function getReplyJsonObjects ($replies, $users) {
        $rereplyManager = $this->modelManagerFactory->getModelManager('rereply');

        $finalResult = array();
        foreach($replies as $reply) {
            $arr = $reply->asArray(array_diff($rereplyManager->getAllFields(), array('user_id')));
            foreach($users as $user) {
                if($user->getUserId() == $reply->getUserId()) {
                    $arr['user'] = $user->asArray();
                    break;
                }
            }

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

}