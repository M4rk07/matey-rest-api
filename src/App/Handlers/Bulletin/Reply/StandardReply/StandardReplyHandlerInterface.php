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
interface StandardReplyHandlerInterface
{
    public function handleCreateReply (Application $app, Request $request, $postId);
    public function handleDeleteReply (Application $app, Request $request, $replyId);
    public function handleGetReplies (Application $app, Request $request, $postId);

}