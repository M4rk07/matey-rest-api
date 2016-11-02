<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.13
 */

namespace App\Controllers;

use App\Algos\ActivityWeights;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookRequest;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;



class FollowerController extends AbstractController
{

    public function followerAction (Request $request, $action) {

        // fetch values from request
        $fromUser = $request->request->get("user_id");
        $usersToFollow = $request->getContent();
        $usersToFollow = json_decode($usersToFollow);
        // validate values from request,
        // for user id it must be numeric string
        $this->validateNumericUnsigned($fromUser);

        foreach ($usersToFollow as $user) {
            $this->validateNumericUnsigned($user->user_id);
            if(strcasecmp($fromUser, $user->user_id) == 0) throw new InvalidRequestException();
            if($action == "follow") $this->follow($fromUser, $user->user_id);
            else if ($action == "unfollow") $this->unfollow($fromUser, $user->user_id);
            else throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        return $this->returnOk();

    }

    public function follow ($fromUser, $toUser) {
        // create follow in database
        $this->service->createFollow($fromUser, $toUser);
            // store follow in redis
        $this->redisService->incrUserNumOfFollowers($toUser, 1);
        $this->redisService->incrUserNumOfFollowing($fromUser, 1);
        $this->redisService->incrUserRelationship($fromUser, $toUser, ActivityWeights::FOLLOW_SCORE, $this->returnTime());
        $this->redisService->pushNewConnection($fromUser, $toUser);
        /*
         * Push just followed user activities to following user newsfeed
         */
        $followedUserActivities = $this->service->getActivityIdsByUser($toUser, 30);
        if(!empty($followedUserActivities))
            $this->redisService->pushActivitiesToOneFeed($followedUserActivities, $fromUser);

    }

    public function unfollow ($fromUser, $toUser) {
        // remove follow in database
        $this->service->deleteFollow($fromUser, $toUser);
        // remove follow in redis
        $this->redisService->incrUserNumOfFollowers($toUser, -1);
        $this->redisService->incrUserNumOfFollowing($fromUser, -1);
        $this->redisService->deleteConnection($fromUser, $toUser);
    }

}