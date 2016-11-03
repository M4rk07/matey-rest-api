<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.13
 */

namespace App\Controllers;

use App\Algos\ActivityWeights;
use App\Algos\Timer;
use App\MateyManagers\FollowManager;
use App\MateyManagers\UserManager;
use App\MateyModels\Follow;
use App\MateyModels\User;
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
        $fromUserId = $request->request->get("user_id");
        $usersToFollow = $request->getContent();
        $usersToFollow = json_decode($usersToFollow);
        // validate values from request,
        // for user id it must be numeric string
        $this->validateNumericUnsigned($fromUserId);
        $fromUser = new User();
        $fromUser->setUserId($fromUserId);

        foreach ($usersToFollow as $user) {
            $this->validateNumericUnsigned($user->user_id);
            if(strcasecmp($fromUserId, $user->user_id) == 0) throw new InvalidRequestException();

            $toUser = new User();
            $toUser->setUserId($user->user_id);

            if($action == "follow") $this->follow($fromUser, $toUser);
            else if ($action == "unfollow") $this->unfollow($fromUser, $toUser);
            else throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        return $this->returnOk();

    }

    public function follow (User $fromUser, User $toUser) {
        // create follow in database
        $follow = new Follow();
        $follow->setUserFrom($fromUser->getUserId())
            ->setUserTo($toUser->getUserId())
            ->setDateTime(Timer::returnTime());
        $followManager = new FollowManager();
        $followManager->createFollow($follow);

        $userManager = new UserManager();
        $userManager->incrUserNumOfFollowing($fromUser, 1);
        $userManager->incrUserNumOfFollowers($toUser, 1);
        /*
         * Push just followed user activities to following user newsfeed
         */
        $followedUserActivities = $userManager->getUserActivities($toUser, 30);

        if(!empty($followedUserActivities))
            $userManager->pushActivitiesToUserFeed($followedUserActivities, $fromUser);

    }

    public function unfollow (User $fromUser, User $toUser) {
        // remove follow in database
        $follow = new Follow();
        $follow->setUserFrom($fromUser->getUserId())
            ->setUserTo($toUser->getUserId());
        $followManager = new FollowManager();
        $followManager->deleteFollow($follow);

        $userManager = new UserManager();
        $userManager->incrUserNumOfFollowing($fromUser, -1);
        $userManager->incrUserNumOfFollowers($toUser, -1);
    }

}