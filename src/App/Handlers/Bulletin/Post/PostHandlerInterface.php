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

    public function createPost (Application $app, Request $request);
    public function deletePost (Application $app, Request $request, $postId);
    public function getPost (Application $app, Request $request, $postId);
    public function getPosts(Application $app, Request $request, $type, $id);
    public function boost (Application $app, Request $request, $postId);
    public function share (Application $app, Request $request, $postId);
    public function bookmark (Application $app, Request $request, $postId);
    public function archive (Application $app, Request $request, $postId);

}