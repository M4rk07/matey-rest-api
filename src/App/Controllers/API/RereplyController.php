<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.3.17.
 * Time: 00.36
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Bulletin\Rereply\RereplyHandler;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RereplyController extends AbstractController
{
    protected $rereplyHandler;

    public function __construct(
        RereplyHandler $rereplyHandler
    ) {
        $this->rereplyHandler = $rereplyHandler;
    }

    public function createRereplyAction (Application $app, Request $request, $replyId) {
        return $this->rereplyHandler
            ->createRereply($app, $request, $replyId);
    }

    public function deleteRereplyAction (Application $app, Request $request, $rereplyId) {
        return $this->rereplyHandler
            ->deleteRereply($app, $request, $rereplyId);
    }

    public function getRerepliesAction (Application $app, Request $request, $replyId) {
        return $this->rereplyHandler
            ->getRereplies($app, $request, $replyId);
    }

}