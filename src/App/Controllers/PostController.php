<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.20
 */

namespace App\Controllers;

use App\Algos\ActivityWeights;
use App\Algos\Timer;
use App\MateyManagers\ActivityManager;
use App\MateyManagers\PostManager;
use App\MateyManagers\ResponseManager;
use App\MateyManagers\UserManager;
use App\MateyModels\Activity;
use App\MateyModels\Post;
use App\MateyModels\Response;
use App\MateyModels\User;
use App\Security\IdGenerator;
use App\Services\BaseService;
use App\Services\FollowerService;
use App\Services\InterestService;
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

        $this->validate($text, [
            new NotBlank()
        ]);

        $activity = new Activity();
        $post = new Post();
        $user = new User();

        $postManager = new PostManager();
        $activityManager = new ActivityManager();
        $userManager = new UserManager();

        $activity->setUserId($user_id)
            ->setActivityType(BaseService::TYPE_POST)
            ->setActivityTime(Timer::returnTime());
        $post->setUserId($user_id)
            ->setText($text)
            ->setDateTime(Timer::returnTime());
        $user->setUserId($user_id);


        $this->service->startTransaction();
        try {
            $post = $postManager->createPost($post);

            $activity->setSourceId($post->getPostId())
                ->setSrlData($post->serialize());

            $activity = $activityManager->createActivity($activity);

            $activityManager->pushToNewsFeeds($activity, $user);
            $userManager->incrUserNumOfPosts($user, 1);

            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk(array(
            "post_id" => $post->getPostId()
        ));

    }

    public function deletePostAction(Request $request) {
        $user_id = $request->request->get("user_id");
        $post_id = $request->request->get("post_id");

        $this->validateNumericUnsigned($post_id);

        $activity = new Activity();
        $post = new Post();
        $user = new User();

        $postManager = new PostManager();
        $activityManager = new ActivityManager();
        $userManager = new UserManager();

        $activity->setUserId($user_id)
            ->setSourceId($post_id)
            ->setActivityType(BaseService::TYPE_POST);
        $post->setUserId($user_id)
            ->setPostId($post_id);
        $user->setUserId($user_id);

        $this->service->startTransaction();
        try {
            $postManager->deletePost($post);
            $activityManager->deleteActivity($activity);
            $userManager->incrUserNumOfPosts($user, -1);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk();

    }



}