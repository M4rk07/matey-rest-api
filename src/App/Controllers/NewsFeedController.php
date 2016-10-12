<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.40
 */

namespace App\Controllers;



use Predis\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NewsFeedController
{
    protected $redis;

    public function returnNewsFeed (Request $request) {

        $user_id = $request->request->get("user_id");
        $from = $request->request->get("from");
        $to = $request->request->get("to");


    }

}