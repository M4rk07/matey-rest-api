<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 5.3.17.
 * Time: 22.13
 */

namespace App\Handlers\Feed;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

interface FeedHandlerInterface
{

    public function getFeed(Application $app, Request $request);

}