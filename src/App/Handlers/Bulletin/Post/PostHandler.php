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
        $userId = $request->request->get('user_id');

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
            $this->createActivity($post->getPostId(), $userId, $jsonData['group_id'], Activity::GROUP_TYPE, Activity::POST_TYPE);
            if($post->getLocationsNum() > 0) {
                $this->insertLocations($jsonData['locations'], $post->getPostId(), Activity::POST_TYPE);
            }

            // Commiting transaction on success
            $postManager->commitTransaction();
        } catch (\Exception $e) {
            // Rollback transaction on failure
            $postManager->rollbackTransaction();
            throw new ServerErrorException();
        }

        // Calling the service for uploading Post attachments to S3 storage
        if(strpos($contentType, 'multipart/form-data') === 0) {
            $app['matey.file_handler.factory']->getFileHandler('post_attachment')->upload($app, $request, $post->getPostId());
        }

        // Pushing newly created post to news feed of followers
        $this->pushPostToDecks($post);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();
        $user->setUserId($userId);
        $userManager->incrNumOfPosts($user);

        $postResult = $this->getPosts(array(
            'post_id' => $post->getPostId()
        ), 1);
        $finalResult['data'] = $postResult[0];

        return new JsonResponse($finalResult, 200);

    }

    public function handleDeletePost (Application $app, Request $request, $postId) {
        // Get user id based on token
        $userId = $request->request->get('user_id');

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();

        $post->setDeleted(1);

        $postManager->updateModel($post, array(
            'post_id' => $postId,
            'user_id' => $userId
        ));

        return new JsonResponse(null, 200);
    }

    public function handleGetSinglePost ($postId) {
        return $this->getPosts(array(
            'post_id' => $postId
        ), 1);
    }

    public function handleGetPostsByOwner(Request $request, $ownerType, $id) {
        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::POSTS_LIMIT
        ));

        if($ownerType == 'user') $criteria['user_id'] = $id;
        else $criteria['group_id'] = $id;

        if(!empty($pagParams['max_id'])) $criteria['post_id:<'] = $pagParams['max_id'];

        $postResult = $this->getPosts($criteria, $pagParams['count']);

        if(($resultNum = count($postResult)) > 0)
            $nextMaxId = $postResult[$resultNum-1]['post_id'];
        else $nextMaxId=null;

        $paginationService = new PaginationService($postResult, $nextMaxId, $pagParams['count'],
            '/groups/'.$id.'/posts');

        return new JsonResponse($paginationService->getResponse(), 200);
    }

    public function handleBoost (Application $app, Request $request, $postId) {
        $userId = $request->request->get('user_id');

        $boostManager = $this->modelManagerFactory->getModelManager('boost');
        $boost = $boostManager->getModel();
        $boost->setUserId($userId)
            ->setPostId($postId);
        $boostManager->createModel($boost);

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();
        $post->setPostId($postId);
        $postManager->incrNumOfBoosts($post);

        $post = $postManager->readModelOneBy(array(
            'post_id' => $postId
        ), null, array('group_id'));
        $this->createActivity($postId, $userId, $post->getGroupId(), Activity::GROUP_TYPE, Activity::BOOST_TYPE);

        return new JsonResponse(null, 200);
    }

    public function handleShare (Application $app, Request $request, $postId) {
        $userId = $request->request->get('user_id');

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
        $this->createActivity($postId, $userId, $post->getGroupId(), Activity::GROUP_TYPE, Activity::SHARE_TYPE);

        return new JsonResponse(null, 200);
    }

    public function handleBookmark (Application $app, Request $request, $postId) {
        $userId = $request->request->get('user_id');

        $bookmarkManager = $this->modelManagerFactory->getModelManager('bookmark');
        $bookmark = $bookmarkManager->getModel();
        $bookmark->setUserId($userId)
            ->setPostId($postId);
        $bookmarkManager->createModel($bookmark);

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();
        $post->setPostId($postId);
        $postManager->incrNumOfBookmarks($post);

        $post = $postManager->readModelOneBy(array(
            'post_id' => $postId
        ), null, array('group_id'));
        $this->createActivity($postId, $userId, $post->getGroupId(), Activity::GROUP_TYPE, Activity::BOOKMARK_TYPE);

        return new JsonResponse(null, 200);
    }

    public function handleArchive (Application $app, Request $request, $postId) {
        $userId = $request->request->get('user_id');

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
        $this->createActivity($postId, $userId, $post->getGroupId(), Activity::GROUP_TYPE, Activity::ARCHIVE_TYPE);

        return new JsonResponse(null, 200);
    }

    public function handleGetDeck (Application $app, Request $request, $groupId = null) {

        $userId = $request->request->get('user_id');
        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::POSTS_LIMIT
        ));

        $finalResult = $this->getDeck($userId, $groupId, $pagParams);

        $nextMaxId = $finalResult[count($finalResult)-1]['activity_object']['post_id'];

        if($groupId !== null)
            $paginationService = new PaginationService($finalResult, $nextMaxId, $pagParams['count'], '/groups/'.$groupId.'/deck');
        else
            $paginationService = new PaginationService($finalResult, $nextMaxId, $pagParams['count'], '/deck');

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

        $maxIdKey = 0;
        if(!empty($pagParams['max_id'])) $maxIdKey = (int)(array_search($pagParams['max_id'], $allPostIds)) + 1;

        $postIds = array();
        for($i = $maxIdKey; $i < $maxIdKey + $pagParams['count']; $i++) {
            $postIds[] = $allPostIds[$i];
        }

        $finalPosts = $this->getPosts(array(
            'post_id' => $postIds
        ), $pagParams['count']);

        $finalResult = array();
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