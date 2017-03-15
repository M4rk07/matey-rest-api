<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 23.12
 */

namespace App\Controllers\API;

use App\Controllers\AbstractController;
use App\Handlers\Bulletin\Post\PostHandlerInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class PostController extends AbstractController
{
    protected $postHandler;

    public function __construct(
        PostHandlerInterface $postHandler
    ) {
        $this->postHandler = $postHandler;
    }

    public function createPostAction (Application $app, Request $request) {
        return $this->postHandler
            ->handleCreatePost($app, $request);
    }

    public function deletePostAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->handleDeletePost($app, $request, $postId);
    }

    public function getPostAction (Application $app, Request $request, $postId) {
        $postResult = $this->postHandler
            ->handleGetSinglePost($request, $postId);

        $replyController = $app['matey.reply_controller'];
        $repliesResult = $replyController->getRepliesAction($app, $request, $postId);
        if($repliesResult->getStatusCode() !== 200) return $repliesResult;

        $finalResult['data'] = $postResult[0];
        $finalResult['data']['replies'] = json_decode($repliesResult->getContent());

        return new JsonResponse($finalResult, 200);
    }

    public function getGroupPostsAction (Application $app, Request $request, $groupId) {
        return $this->postHandler
        ->handleGetPostsByOwner($request, 'group', $groupId);
    }

    public function getUserPostsAction (Application $app, Request $request, $userId) {
        return $this->postHandler
            ->handleGetPostsByOwner($request, 'user', $userId);
    }

    public function boostAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->handleBoost($app, $request, $postId);
    }

    public function shareAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->handleShare($app, $request, $postId);
    }

    public function bookmarkAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->handleBookmark($app, $request, $postId);
    }

    public function archiveAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->handleArchive($app, $request, $postId);
    }

    public function getUserDeckAction (Application $app, Request $request) {
        return $this->postHandler
            ->handleGetDeck($app, $request);
    }

    public function getGroupDeckAction (Application $app, Request $request, $groupId) {
        return $this->postHandler
            ->handleGetDeck($app, $request, $groupId);
    }

}