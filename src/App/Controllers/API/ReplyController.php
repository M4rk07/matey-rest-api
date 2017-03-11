<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.3.17.
 * Time: 00.36
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Bulletin\Reply\ReplyHandler;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ReplyController extends AbstractController
{
    protected $replyHandler;

    public function __construct(
        ReplyHandler $replyHandler
    ) {
        $this->replyHandler = $replyHandler;
    }

    public function createReplyAction (Application $app, Request $request, $postId) {
        return $this->replyHandler
            ->createReply($app, $request, $postId);
    }

    public function deleteReplyAction (Application $app, Request $request, $replyId) {
        return $this->replyHandler
            ->deleteReply($app, $request, $replyId);
    }

    public function getRepliesAction (Application $app, Request $request, $postId) {
        return $this->replyHandler
            ->getReplies($app, $request, $postId);
    }
}