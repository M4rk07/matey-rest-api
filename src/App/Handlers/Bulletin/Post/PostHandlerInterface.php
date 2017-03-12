<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 16.12
 */

namespace App\Handlers\Bulletin\Post;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

interface PostHandlerInterface
{

    public function handleCreatePost (Application $app, Request $request);
    public function handleDeletePost (Application $app, Request $request, $postId);
    public function handleGetSinglePost(Application $app, Request $request, $postId);
    public function handleGetPosts(Application $app, Request $request, $type, $id);
    public function handleBoost (Application $app, Request $request, $postId);
    public function handleShare (Application $app, Request $request, $postId);
    public function handleBookmark (Application $app, Request $request, $postId);
    public function handleArchive (Application $app, Request $request, $postId);
    public function handleGetDeck (Application $app, Request $request, $groupId = null);

}