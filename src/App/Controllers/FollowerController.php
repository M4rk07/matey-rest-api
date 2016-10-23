<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.13
 */

namespace App\Controllers;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;



class FollowerController extends AbstractController
{

    public function followerAction (Request $request, $action) {

        // fetch values from request
        $fromUser = $request->request->get("user_id");
        $toUser = $request->request->get("to_user");

        // validate values from request,
        // for user id it must be numeric string
        if($fromUser == $toUser) throw new InvalidRequestException();
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

        if($action == "follow") $this->follow($fromUser,$toUser);
        else if ($action == "unfollow") $this->unfollow($fromUser, $toUser);
        else throw new InvalidRequestException([
            'error_description' => 'The request includes an invalid parameter value.',
        ]);

        return $this->returnOk();

    }

    public function follow ($fromUser, $toUser) {
        // create follow in database
        $this->service->createFollow($fromUser, $toUser);
        // store follow in redis
        $this->redisService->incrUserNumOfFollowers($toUser, 1);
        $this->redisService->incrUserNumOfFollowing($fromUser, 1);
    }

    public function unfollow ($fromUser, $toUser) {
        // remove follow in database
        $this->service->deleteFollow($fromUser, $toUser);
        // remove follow in redis
        $this->redisService->incrUserNumOfFollowers($toUser, -1);
        $this->redisService->incrUserNumOfFollowing($fromUser, -1);
    }

}