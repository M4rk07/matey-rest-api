<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.3.17.
 * Time: 17.18
 */

namespace App\Handlers\Bulletin\Reply;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

interface ReplyHandlerInterface
{
    public function approve(Application $app, Request $request, $type, $id);
}