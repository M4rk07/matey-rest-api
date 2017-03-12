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
use App\Handlers\Post\PostHandlerFactoryInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        return $this->postHandler
            ->handleGetSinglePost($app, $request, $postId);
    }

    public function getGroupPostsAction (Application $app, Request $request, $groupId) {
        return $this->postHandler
            ->handleGetPosts($app, $request, 'group', $groupId);
    }

    public function getUserPostsAction (Application $app, Request $request, $userId) {
        return $this->postHandler
            ->handleGetPosts($app, $request, 'user', $userId);
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