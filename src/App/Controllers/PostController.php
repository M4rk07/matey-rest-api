<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.20
 */

namespace App\Controllers;

use App\Security\IdGenerator;
use App\Services\BaseService;
use App\Services\FollowerService;
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
        $interest_id = $request->request->get("interest_id");

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($interest_id, [
            new NotBlank(),
        ]);
        $this->validate($text, [
            new NotBlank()
        ]);

        $post_id = $this->service->createPost($interest_id, $user_id, $text);
        $srl_data = serialize(
            array(
                "post_id" => $post_id,
                "text" => $text
            )
        );

        $time = $this->returnTime();
        $activity_id = $this->service->createActivity($user_id, $post_id, BaseService::TYPE_POST, $interest_id, BaseService::TYPE_INTEREST, $srl_data);
        $this->redisService->pushToNewsFeeds($activity_id, $time, $user_id);
        $this->redisService->incrUserNumOfPosts($user_id, 1);
        $this->redisService->initializePostStatistics($post_id);

        return $this->returnOk(array("post_id" => $post_id));

    }

    public function deletePostAction(Request $request) {
        $user_id = $request->request->get("user_id");
        $post_id = $request->request->get("post_id");

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($post_id, [
            new NotBlank()
        ]);

        $this->service->deletePost($post_id, $user_id);
        $this->service->deleteActivity($post_id, BaseService::TYPE_POST);
        $this->redisService->deletePostStatistics($post_id);
        $this->redisService->incrUserNumOfPosts($user_id, -1);

        return $this->returnOk();

    }

    public function bookmarkPostAction (Request $request) {
        $user_id = $request->request->get("user_id");
        $post_id = $request->request->get("post_id");

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($post_id, [
            new NotBlank()
        ]);

        $this->service->bookmarkPost($post_id, $user_id);
        $this->redisService->pushPostBookmark($post_id, $user_id);

        return $this->returnOk();

    }

    public function addResponseAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $text = $request->request->get("text");
        $post_id = $request->request->get("post_id");

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($text, [
            new NotBlank()
        ]);
        $this->validate($post_id, [
            new NotBlank()
        ]);

        $response_id = $this->service->createResponse($user_id, $post_id, $text);
        $srl_data = serialize(
            array(
                "response_id" => $response_id,
                "text" => $text
            )
        );
        $time = $this->returnTime();
        $activity_id = $this->service->createActivity($user_id, $response_id, BaseService::TYPE_RESPONSE, $post_id, BaseService::TYPE_POST, $srl_data);
        $this->redisService->pushToNewsFeeds($activity_id, $time, $user_id);
        $this->redisService->initializeResponseStatistics($response_id);
        $this->redisService->incrPostNumOfResponses($post_id, 1);
        $this->redisService->incrUserNumOfResponses($user_id, 1);
        $this->redisService->pushLastResponseToPost($post_id, $user_id);

        return $this->returnOk(array("response_id" => $response_id));

    }

    public function deleteResponseAction(Request $request) {

        $response_id = $request->request->get("response_id");
        $post_id = $request->request->get("post_id");
        $user_id = $request->request->get("user_id");

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($response_id, [
            new NotBlank()
        ]);
        $this->validate($post_id, [
            new NotBlank()
        ]);

        $this->service->deleteResponse($response_id, $post_id, $user_id);
        $this->service->deleteActivity($response_id, BaseService::TYPE_RESPONSE);
        $this->redisService->incrPostNumOfResponses($post_id, -1);
        $this->redisService->incrUserNumOfResponses($user_id, -1);
        $this->redisService->deleteResponseStatistics($response_id);

        return $this->returnOk();

    }

    public function approveAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $response_id = $request->request->get("response_id");

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($response_id, [
            new NotBlank()
        ]);

        $this->service->approve($user_id, $response_id);
        $this->redisService->incrResponseNumOfApproves($response_id, 1);

        return $this->returnOk();

    }



}