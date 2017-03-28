<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 16.10
 */

namespace App\Handlers\Bulletin\Post;

use App\Algos\FeedRank\FeedRank;
use App\Constants\Defaults\DefaultDates;
use App\Constants\Defaults\DefaultNumbers;
use App\Constants\Messages\ResponseMessages;
use App\Handlers\File\PostAttachmentHandler;
use App\MateyModels\Activity;
use App\MateyModels\FeedEntry;
use App\MateyModels\Group;
use App\MateyModels\Post;
use App\Services\PaginationService;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Mockery\CountValidator\Exception;
use Mockery\Matcher\Not;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostHandler extends AbstractPostHandler
{

    public function handleCreatePost(Application $app, Request $request) {
        // Get user id based on token
        $userId = self::getTokenUserId($request);

        // Getting json data in relation to Content-Type
        $contentType = $request->headers->get('Content-Type');

        $this->validateNumOfFiles($request);
        $jsonDataRequest = $this->getJsonPostData($request, $contentType);

        $jsonData = array();
        $jsonData['title'] = $this->gValidateTitle($jsonDataRequest);
        $jsonData['text'] = $this->gValidateText($jsonDataRequest);
        $jsonData['group_id'] = $this->gValidateGroupId($jsonDataRequest);
        $jsonData['locations'] = $this->gValidateLocations($jsonDataRequest);

        // Creating necessary data managers.
        $postManager = $this->modelManagerFactory->getModelManager('post');
        $activityManager = $this->modelManagerFactory->getModelManager('activity');
        $post = $postManager->getModel();

        // Creating a Post model
        $post->setTitle($jsonData['title'])
            ->setText($jsonData['text'])
            ->setAttachsNum($request->files->count())
            ->setLocationsNum(count($jsonData['locations']))
            ->setUserId($userId)
            ->setGroupId($jsonData['group_id']);

        // Starting transaction
        $postManager->startTransaction();
        try {
            // Writing Post model to database
            $post = $postManager->createModel($post);
            $this->createActivity($userId, $post->getPostId(), Activity::POST_TYPE, $jsonData['group_id'], Activity::GROUP_TYPE, Activity::POST_CREATE_ACT);
            if($post->getLocationsNum() > 0) {
                $this->insertLocations($jsonData['locations'], $post->getPostId(), Activity::POST_TYPE);
            }
            $app['matey.search_service']->addPostToSearch($post);

            // Commiting transaction on success
            $postManager->commitTransaction();
        } catch (\Exception $e) {
            // Rollback transaction on failure
            $postManager->rollbackTransaction();
            throw $e;
        }

        // Calling the service for uploading Post attachments to S3 storage
        if(strpos($contentType, 'multipart/form-data') === 0) {
            $app['matey.file_handler.factory']->getFileHandler('post_attachment')->upload($app, $request, $post->getPostId(), PostAttachmentHandler::LOCATION_POSTS);
        }

        // Pushing newly created post to news feed of followers
        $this->pushPostToDecks($post);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();
        $user->setUserId($userId);
        $userManager->incrNumOfPosts($user);

        $postResult = $this->getPosts(array(
            'post_id' => $post->getPostId()
        ), $userId, 1);
        $finalResult['data'] = $postResult[0];

        return new JsonResponse($finalResult, 200);

    }

    public function handleDeletePost (Application $app, Request $request, $postId) {
        // Get user id based on token
        $userId = self::getTokenUserId($request);

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();

        $post->setDeleted(1);

        $postManager->updateModel($post, array(
            'post_id' => $postId,
            'user_id' => $userId
        ));

        return new JsonResponse(null, 200);
    }

    public function handleGetSinglePost (Request $request, $postId) {
        $userId = self::getTokenUserId($request);

        return $this->getPosts(array(
            'post_id' => $postId
        ), $userId, 1);
    }

    public function handleGetPostsByOwner(Request $request, $ownerType, $id) {
        $userId = self::getTokenUserId($request);

        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::POSTS_LIMIT
        ));

        if($ownerType == 'user') $criteria['user_id'] = $id;
        else $criteria['group_id'] = $id;

        if(!empty($pagParams['max_id'])) $criteria['post_id:<'] = $pagParams['max_id'];

        $postResult = $this->getPosts($criteria, $userId, $pagParams['count']);

        $paginationService = new PaginationService($postResult, $pagParams['count'],
            $ownerType == 'group' ? '/groups/'.$id.'/posts' : '/users/'.$id.'/posts', 'post_id');

        return new JsonResponse($paginationService->getResponse(), 200);
    }

    public function handleBoost (Application $app, Request $request, $postId) {
        $userId = self::getTokenUserId($request);
        $method = $request->getMethod();

        $boostManager = $this->modelManagerFactory->getModelManager('boost');
        $boost = $boostManager->getModel();
        $boost->setUserId($userId)
            ->setPostId($postId);

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();
        $post->setPostId($postId);

        if($method == 'PUT') {
            $boostManager->createModel($boost);
            $postManager->incrNumOfBoosts($post);
            $post = $postManager->readModelOneBy(array(
                'post_id' => $postId
            ), null, array('group_id'));
            $this->createActivity($userId, $postId, Activity::POST_TYPE, $post->getGroupId(), Activity::GROUP_TYPE, Activity::BOOST_ACT);
        }
        else {
            $boostManager->deleteModel($boost);
            $postManager->incrNumOfBoosts($post, -1);
        }

        return new JsonResponse(null, 200);
    }

    public function handleGetUsersBoosted(Application $app, Request $request, $postId) {
        $boostManager = $this->modelManagerFactory->getModelManager('boost');
        $userManager = $this->modelManagerFactory->getModelManager('user');

        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::POSTS_LIMIT
        ));

        $criteria['post_id'] = $postId;
        if(!empty($pagParams['max_id'])) $criteria['user_id:<'] = $pagParams['max_id'];

        $boosts = $boostManager->readModelBy($criteria, array('user_id' => 'DESC'), $pagParams['count']);

        $userIds = array();
        foreach($boosts as $boost) {
            $userIds[] = $boost->getUserId();
        }

        $users = $userManager->readModelBy(array(
            'user_id' => $userIds
        ), array('user_id' => 'DESC'), $pagParams['count'], null, array('user_id', 'fist_name', 'last_name', 'picture_url'));
        
        $finalResult = array();
        foreach ($users as $user) {
            $finalResult[] = $user->asArray();
        }

        $paginationService = new PaginationService($finalResult, $pagParams['count'],
            '/posts/'.$postId.'/boosts', 'user_id');

        return new JsonResponse($paginationService->getResponse(), 200);
    }

    public function handleShare (Application $app, Request $request, $postId) {
        $userId = self::getTokenUserId($request);

        $shareManager = $this->modelManagerFactory->getModelManager('share');
        $share = $shareManager->getModel();
        $share->setUserId($userId)
            ->setParentId($postId)
            ->setParentType(Activity::POST_TYPE);
        $shareManager->createModel($share);

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();
        $post->setPostId($postId);
        $postManager->incrNumOfShares($post);

        $post = $postManager->readModelOneBy(array(
            'post_id' => $postId
        ), null, array('group_id'));
        $this->createActivity($userId, $postId, Activity::POST_TYPE, $post->getGroupId(), Activity::GROUP_TYPE, Activity::SHARE_ACT);

        return new JsonResponse(null, 200);
    }

    public function handleBookmark (Application $app, Request $request, $postId) {
        $userId = self::getTokenUserId($request);
        $method = $request->getMethod();

        $bookmarkManager = $this->modelManagerFactory->getModelManager('bookmark');
        $bookmark = $bookmarkManager->getModel();
        $bookmark->setUserId($userId)
            ->setPostId($postId);

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();
        $post->setPostId($postId);

        if($method == 'PUT') {
            $bookmarkManager->createModel($bookmark);
            $postManager->incrNumOfBoosts($post);
            $post = $postManager->readModelOneBy(array(
                'post_id' => $postId
            ), null, array('group_id'));
            $this->createActivity($userId, $postId, Activity::POST_TYPE, $post->getGroupId(), Activity::GROUP_TYPE, Activity::BOOKMARK_ACT);
        }
        else {
            $bookmarkManager->deleteModel($bookmark);
            $postManager->incrNumOfBookmarks($post, -1);
        }

        return new JsonResponse(null, 200);
    }

    public function handleArchive (Application $app, Request $request, $postId) {
        $userId = self::getTokenUserId($request);

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();
        $post->setArchived(1);
        $postManager->updateModel($post, array(
            'post_id' => $postId
        ));

        $post = $postManager->readModelOneBy(array(
            'post_id' => $postId,
            'archived' => 1
        ), null, array('group_id'));
        $this->createActivity($userId, $postId, Activity::POST_TYPE, $post->getGroupId(), Activity::GROUP_TYPE, Activity::ARCHIVE_ACT);

        return new JsonResponse(null, 200);
    }

    public function handleGetDeck (Application $app, Request $request, $groupId = null) {

        $userId = self::getTokenUserId($request);
        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::POSTS_LIMIT
        ));

        $finalResult = $this->getDeck($userId, $groupId, $pagParams);

        if($groupId !== null)
            $paginationService = new PaginationService($finalResult, $pagParams['count'],
                '/groups/'.$groupId.'/deck', array('activity_object', 'post_id'));
        else
            $paginationService = new PaginationService($finalResult, $pagParams['count'],
                '/deck', array('activity_object', 'post_id'));

        return new JsonResponse($paginationService->getResponse(), 200);

    }

    public function getDeck ($userId, $groupId, $pagParams) {

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $user = $userManager->getModel();
        $group = $groupManager->getModel();
        $user->setUserId($userId);
        $group->setGroupId($groupId);

        if($groupId !== null)
            $allPostIds = $groupManager->getDeck($group);
        else
            $allPostIds = $userManager->getDeck($user);

        $finalResult = array();
        $maxIdKey = 0;
        if(!empty($pagParams['max_id'])) {
            $index = array_search($pagParams['max_id'], $allPostIds);
            if($index !== false) {
                $maxIdKey = (int)$index + 1;
            } else return $finalResult;
        }

        $postIds = array();
        for($i = $maxIdKey; $i < $maxIdKey + $pagParams['count']; $i++) {
            if(!isset($allPostIds[$i])) break;
            $postIds[] = $allPostIds[$i];
        }

        if(empty($postIds)) return array();

        $finalPosts = $this->getPosts(array(
            'post_id' => $postIds
        ), $userId, $pagParams['count']);

        foreach($finalPosts as $finalPost) {
            $arr['activity_type'] = Activity::POST_TYPE;
            $arr['activity_object'] = $finalPost;

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

    // Method for pushing newly created Post to Feeds
    public function pushPostToDecks (Post $post) {

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

        $this->pushToDecks ($userFollowers, $post);

    }

    public function pushToDecks ($users, $posts) {
        if(!is_array($posts)) $posts = array($posts);
        // push to users decks
        $userManager = $this->modelManagerFactory->getModelManager('user');
        foreach($users as $user) {
            $userManager->pushDeck($user, $posts);
        }

        // push to group decks
        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $group = $groupManager->getModel();
        foreach($posts as $post) {
            if ($post->getGroupId() !== null) {
                $group->setGroupId($post->getGroupId());
                $groupManager->pushDeck($group, $post);
            }
        }

    }

}