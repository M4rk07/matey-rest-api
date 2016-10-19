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
use Symfony\Component\Validator\Constraints\Type;

class PostController extends AbstractController
{

    public function addPostAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $text = $request->request->get("text");

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

        $idGenerator = new IdGenerator();
        $post_id = $idGenerator->generatePostId($user_id);
        $this->service->createPost($post_id, $user_id, $text);

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

        $idGenerator = new IdGenerator();
        $response_id = $idGenerator->generateResponseId($user_id);
        $this->service->createResponse($response_id, $user_id, $post_id, $text);

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

        return $this->returnOk();

    }



}