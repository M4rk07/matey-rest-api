<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 20.14
 */

namespace App\Handlers\Bulletin\Rereply;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

interface RereplyHandlerInterface
{
    public function createRereply (Application $app, Request $request, $replyId);
    public function deleteRereply (Application $app, Request $request, $rereplyId);
    public function getRereplies (Application $app, Request $request, $replyId);
}