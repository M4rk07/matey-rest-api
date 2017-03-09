<?php

namespace App\Handlers\Feed;
use App\Algos\FeedRank\FeedRank;
use App\Constants\Defaults\DefaultDates;
use App\MateyModels\Activity;
use App\MateyModels\FeedEntry;
use App\MateyModels\Group;
use App\MateyModels\Post;
use App\MateyModels\PostManager;
use App\MateyModels\User;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 5.3.17.
 * Time: 22.12
 */
class FeedHandler extends AbstractFeedHandler
{
    public function getFeed (Application $app, Request $request) {

        $userId = $request->request->get('user_id');
        $limit = $request->query->get('limit');
        $offset = $request->query->get('offset');

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $postManager = $this->modelManagerFactory->getModelManager('post');
        $locationManager = $this->modelManagerFactory->getModelManager('location');
        $user = $userManager->getModel();
        $user->setUserId($userId);

        $posts = $this->getPosts($limit, $offset, $user, $postManager, $userManager);
        $users = $this->getUsersOwners($posts, $limit, $userManager);

        $finalResult = array();
        foreach($posts as $post) {
            $arr['activity_type'] = Activity::POST_TYPE;
            $post->setUserId(null);
            $arr['activity_object'] = $post->asArray();
            foreach($users as $user) {
                if($user->getUserId() == $post->getUserId()) {
                    $arr['activity_object']['user'] = $user->asArray();
                    break;
                }
            }
            if($post->getAttachsNum() > 0)
                $arr['activity_object']['attachs'] = $post->getAttachsLocation($post->getAttachsNum());
            if($post->getLocationsNum() > 0)
                $arr['activity_object']['locations'] = $this->getLocations($post, $locationManager);

            $finalResult[]= $arr;
        }

        return new JsonResponse($finalResult, 200);

    }

    public function getPosts($limit, $offset, $user, $postManager, $userManager) {
        $postIds = $userManager->getFeed($user, $offset, $offset+$limit);

        return $postManager->readModelBy(array(
            'post_id' => $postIds
        ), array('time_c' => 'DESC'), $limit, null, $postManager->getAllFields());
    }

    public function getUsersOwners($posts, $limit, $userManager) {
        $userIds = array();
        foreach ($posts as $post) {
            $userIds[] = $post->getUserId();
        }

        return $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $limit, null, array('user_id', 'first_name', 'last_name'));
    }

    public function getLocations ($post, $locationManager) {
        $locations = $locationManager->readModelBy(array(
            'parent_id' => $post->getPostId(),
            'parent_type' => Activity::POST_TYPE
        ), null, $post->getLocationsNum(), null, array('latt', 'longt'));
        $arr = array();
        foreach($locations as $location) {
            $arr[] = $location->asArray();
        }

        return $arr;
    }

    // Method for pushing newly created Post to Feeds
    public function pushNewPost (Post $post) {

        $followManager = $this->modelManagerFactory->getModelManager('follow');
        $follows = $followManager->getRelevantFollowers($post);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userFollowers = array();

        foreach($follows as $follow) {
            $user = $userManager->getModel();
            $user->setUserId($follow->getUserId());
            $userFollowers[] = $user;
        }

        $user = $userManager->getModel();
        $user->setUserId($post->getUserId());
        $userFollowers[] = $user;

        $this->push ($userFollowers, $post);

    }

    public function push ($users, $posts) {

        $userManager = $this->modelManagerFactory->getModelManager('user');

        foreach($users as $user) {
            $userManager->pushFeed($user, $posts);
        }

    }

}