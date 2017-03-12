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
use App\MateyModels\Activity;
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
            ->handleCreateRereply($app, $request, $replyId);
    }

    public function deleteRereplyAction (Application $app, Request $request, $rereplyId) {
        return $this->rereplyHandler
            ->handleDeleteRereply($app, $request, $rereplyId);
    }

    public function getRerepliesAction (Application $app, Request $request, $replyId) {
        return $this->rereplyHandler
            ->handleGetRereplies($app, $request, $replyId);
    }

    public function approveAction (Application $app, Request $request, $rereplyId) {
        return $this->rereplyHandler
            ->handleApprove($app, $request, Activity::REREPLY_TYPE, $rereplyId);
    }

}