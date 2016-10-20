<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.13
 */

namespace App\Controllers;

use Predis\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class FollowerController extends AbstractController
{

    public function followAction(Request $request) {

        $fromUser = intval($request->request->get("user_id"));
        $toUser = intval($request->request->get("to_user"));

        $this->validate($fromUser, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($toUser, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);

        $this->service->createFollow($fromUser, $toUser);
        $this->redisService->incrUserNumOfFollowers($toUser, 1);
        $this->redisService->incrUserNumOfFollowing($fromUser, 1);

        return $this->returnOk();
    }

    public function unfollowAction(Request $request) {

        $fromUser = $request->request->get("user_id");
        $toUser = $request->request->get("to_user");

        $this->validate($fromUser, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($toUser, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);

        $this->service->deleteFollow($fromUser, $toUser);
        $this->redisService->incrUserNumOfFollowers($toUser, -1);
        $this->redisService->incrUserNumOfFollowing($fromUser, -1);

        return $this->returnOk();
    }

}