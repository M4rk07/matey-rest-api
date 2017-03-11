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
            ->createPost($app, $request);
    }

    public function deletePostAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->deletePost($app, $request, $postId);
    }

    public function getPostAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->getPost($app, $request, $postId);
    }

    public function getGroupPostsAction (Application $app, Request $request, $groupId) {
        return $this->postHandler
            ->getPosts($app, $request, 'group', $groupId);
    }

    public function getUserPostsAction (Application $app, Request $request, $userId) {
        return $this->postHandler
            ->getPosts($app, $request, 'group', $userId);
    }

    public function boostAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->boost($app, $request, $postId);
    }

    public function shareAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->share($app, $request, $postId);
    }

    public function bookmarkAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->bookmark($app, $request, $postId);
    }

    public function archiveAction (Application $app, Request $request, $postId) {
        return $this->postHandler
            ->archive($app, $request, $postId);
    }

}