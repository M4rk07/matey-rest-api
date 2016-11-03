<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 18.10
 */

namespace App\Controllers;


use App\Algos\ActivityWeights;
use App\Algos\Timer;
use App\MateyManagers\ActivityManager;
use App\MateyManagers\FollowManager;
use App\MateyManagers\PostManager;
use App\MateyManagers\ResponseManager;
use App\MateyManagers\UserManager;
use App\MateyModels\Activity;
use App\MateyModels\Follow;
use App\MateyModels\Post;
use App\MateyModels\Response;
use App\MateyModels\User;
use App\Services\BaseService;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResponseController extends AbstractController
{

    public function addResponseAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $text = $request->request->get("text");
        $post_id = $request->request->get("post_id");
        $user_posted_id = $request->request->get("user_posted_id");

        $this->validateNumericUnsigned($post_id);
        $this->validate($text, [
            new NotBlank()
        ]);

        $post = new Post();
        $userPosted = new User();
        $userRespond = new User();
        $response = new Response();
        $activity = new Activity();
        $follow = new Follow();

        $postManager = new PostManager();
        $userManager = new UserManager();
        $responseManager = new ResponseManager();
        $activityManager = new ActivityManager();
        $followManager = new FollowManager();

        $follow->setUserFrom($user_id)
            ->setUserTo($user_posted_id);
        $post->setUserId($user_posted_id)
            ->setPostId($post_id);
        $userPosted->setUserId($user_posted_id);
        $userRespond->setUserId($user_id);
        $response->setUserId($userRespond->getUserId())
            ->setText($text)
            ->setPostId($post_id)
            ->setDateTime(Timer::returnTime());
        $activity->setUserId($userRespond->getUserId())
            ->setActivityType(BaseService::TYPE_RESPONSE)
            ->setParentType(BaseService::TYPE_POST)
            ->setParentId($post->getPostId())
            ->setActivityTime(Timer::returnTime());

        $this->service->startTransaction();
        try {
            $response = $responseManager->createResponse($response);

            $activity->setSourceId($response->getResponseId())
                ->setSrlData($response->serialize());
            $activity = $activityManager->createActivity($activity);

            $activityManager->pushToNewsFeeds($activity, $userRespond);
            $userManager->incrUserNumOfResponses($userRespond, 1);
            $postManager->pushLastResponseToPost($post, $userRespond);

            $followManager->incrUserRelationship($follow, ActivityWeights::RESPONSE_SCORE);
            $postManager->incrPostNumOfResponses($post, 1);

            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk(array(
            "response_id" => $response->getResponseId()
        ));

    }

    public function deleteResponseAction(Request $request) {

        $response_id = $request->request->get("response_id");
        $post_id = $request->request->get("post_id");
        $user_id = $request->request->get("user_id");

        $this->validateNumericUnsigned($response_id);
        $this->validateNumericUnsigned($post_id);

        $activity = new Activity();
        $post = new Post();
        $userRespond = new User();
        $response = new Response();

        $postManager = new PostManager();
        $activityManager = new ActivityManager();
        $userManager = new UserManager();
        $responseManager = new ResponseManager();

        $activity->setUserId($user_id)
            ->setSourceId($post_id)
            ->setActivityType(BaseService::TYPE_RESPONSE);
        $post->setPostId($post_id);
        $response->setResponseId($response_id)
            ->setUserId($user_id)
            ->setPostId($post_id);
        $userRespond->setUserId($user_id);


        $this->service->startTransaction();
        try {
            $responseManager->deleteResponse($response);
            $activityManager->deleteActivity($activity);
            $userManager->incrUserNumOfResponses($user_id, -1);
            $postManager->incrPostNumOfResponses($post_id, -1);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk();

    }

    public function approveAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $response_id = $request->request->get("response_id");
        $response_owner_id = $request->request->get("response_owner_id");

        $this->validateNumericUnsigned($response_id);

        $userApproves = new User();
        $userOwner = new User();
        $response = new Response();
        $follow = new Follow();

        $responseManager = new ResponseManager();
        $followManager = new FollowManager();

        $follow->setUserFrom($user_id)
            ->setUserTo($response_owner_id);
        $userApproves->setUserId($user_id);
        $userOwner->setUserId($response_owner_id);
        $response->setResponseId($response_id)
            ->setUserId($response_owner_id);

        $this->service->startTransaction();
        try {
            $responseManager->approve($userApproves, $response);
            $responseManager->incrResponseNumOfApproves($response, 1);
            $followManager->incrUserRelationship($follow, ActivityWeights::APPROVE_SCORE);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk();

    }

}