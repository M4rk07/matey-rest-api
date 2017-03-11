<?php
namespace App\Handlers\Bulletin\Reply;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 17.50
 */
interface ReplyHandlerInterface
{
    public function createReply (Application $app, Request $request, $postId);
    public function deleteReply (Application $app, Request $request, $replyId);
    public function getReplies (Application $app, Request $request, $postId);

}