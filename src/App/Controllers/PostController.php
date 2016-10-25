<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.20
 */

namespace App\Controllers;

use App\Algos\ActivityWeights;
use App\Security\IdGenerator;
use App\Services\BaseService;
use App\Services\FollowerService;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Mockery\CountValidator\Exception;
use Predis\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class PostController extends AbstractController
{

    public function addPostAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $text = $request->request->get("text");
        $subinterest_id = $request->request->get("interest_id");

        $this->validateNumericUnsigned($subinterest_id);
        $this->validate($text, [
            new NotBlank()
        ]);

        $time = $this->returnTime();
        $this->service->startTransaction();
        try {
            $post_id = $this->service->createPost($subinterest_id, $user_id, $text);
                $srl_data = serialize(array(
                    "post_id" => $post_id,
                    "text" => $text
                ));
            $activity_id = $this->service->createActivity($user_id, $post_id, BaseService::TYPE_POST, $subinterest_id, BaseService::TYPE_INTEREST, $srl_data);
            $this->redisService->startRedisTransaction();
            try {
                $this->redisService->pushToNewsFeeds($activity_id, $time, $user_id);
                $this->redisService->incrUserNumOfPosts($user_id, 1);
                $this->redisService->initializePostStatistics($post_id);
                // evident user interest
                $this->redisService->incrUserSubinterestStatistic($user_id, $subinterest_id, ActivityWeights::POST_SCORE);
                $this->redisService->commitRedisTransaction();
            } catch (\Exception $e) {
                $this->redisService->rollbackRedisTransaction();
                throw new \Exception();
            }
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk(array("post_id" => $post_id));

    }

    public function deletePostAction(Request $request) {
        $user_id = $request->request->get("user_id");
        $post_id = $request->request->get("post_id");

        $this->validateNumericUnsigned($post_id);

        $this->service->startTransaction();
        try {
            $this->service->deletePost($post_id, $user_id);
            $this->service->deleteActivity($post_id, BaseService::TYPE_POST);
            $this->redisService->incrUserNumOfPosts($user_id, -1);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        try {
            $this->redisService->deletePostStatistics($post_id);
        } catch (\Exception $e) {}

        return $this->returnOk();

    }

    public function bookmarkPostAction (Request $request) {
        $user_id = $request->request->get("user_id");
        $post_id = $request->request->get("post_id");

        $this->validateNumericUnsigned($post_id);

        $this->service->bookmarkPost($post_id, $user_id);
        $this->redisService->pushPostBookmark($post_id, $user_id);

        return $this->returnOk();

    }

    public function addResponseAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $text = $request->request->get("text");
        $post_id = $request->request->get("post_id");
        $subinterest_id = $request->request->get("subinterest_id");
        $user_posted_id = $request->request->get("user_posted_id");

        $this->validateNumericUnsigned($subinterest_id);
        $this->validateNumericUnsigned($post_id);
        $this->validate($text, [
            new NotBlank()
        ]);

        $time = $this->returnTime();
        $this->service->startTransaction();
        try {
            $response_id = $this->service->createResponse($user_id, $post_id, $text);
                $srl_data = serialize(array(
                    "response_id" => $response_id,
                    "text" => $text
                ));
            $activity_id = $this->service->createActivity($user_id, $response_id, BaseService::TYPE_RESPONSE, $post_id, BaseService::TYPE_POST, $srl_data);
            $this->redisService->startRedisTransaction();
            try {
                $this->redisService->pushToNewsFeeds($activity_id, $time, $user_id);
                $this->redisService->initializeResponseStatistics($response_id);
                $this->redisService->incrUserNumOfResponses($user_id, 1);
                $this->redisService->pushLastResponseToPost($post_id, $user_id);
                // evident user interest
                $this->redisService->incrUserSubinterestStatistic($user_id, $subinterest_id, ActivityWeights::RESPONSE_SCORE);
                // evident users relationship
                $this->redisService->incrUserRelationship($user_id, $user_posted_id, ActivityWeights::RESPONSE_SCORE, $time);
                $this->redisService->commitRedisTransaction();
            } catch (\Exception $e) {
                $this->redisService->rollbackRedisTransaction();
                throw new \Exception();
            }
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        try {
            $this->redisService->incrPostNumOfResponses($post_id, 1);
        } catch (\Exception $e) {}

        return $this->returnOk(array("response_id" => $response_id));

    }

    public function deleteResponseAction(Request $request) {

        $response_id = $request->request->get("response_id");
        $post_id = $request->request->get("post_id");
        $user_id = $request->request->get("user_id");

        $this->validateNumericUnsigned($response_id);
        $this->validateNumericUnsigned($post_id);

        $this->service->startTransaction();
        try {
            $this->service->deleteResponse($response_id, $post_id, $user_id);
            $this->service->deleteActivity($response_id, BaseService::TYPE_RESPONSE);
            $this->redisService->incrUserNumOfResponses($user_id, -1);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        try {
            $this->redisService->incrPostNumOfResponses($post_id, -1);
            $this->redisService->deleteResponseStatistics($response_id);
        } catch (\Exception $e) {}

        return $this->returnOk();

    }

    public function approveAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $response_id = $request->request->get("response_id");
        $response_owner_id = $request->request->get("response_owner_id");

        $this->validateNumericUnsigned($response_id);

        $time = $this->returnTime();
        $this->service->startTransaction();
        try {
            $this->service->approve($user_id, $response_id);
            $this->redisService->incrResponseNumOfApproves($response_id, 1);
            $this->redisService->incrUserRelationship($user_id, $response_owner_id, ActivityWeights::APPROVE_SCORE, $time);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk();

    }



}