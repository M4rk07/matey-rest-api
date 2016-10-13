<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.13
 */

namespace App\Controllers;

use Predis\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FollowerController extends AbstractController
{

    public function followAction(Request $request) {

        $fromUser = intval($request->request->get("from_user"));
        $toUser = intval($request->request->get("to_user"));

        $this->service->createFollow($fromUser, $toUser);

        return $this->returnOk();
    }

    public function unfollowAction(Request $request) {

        $fromUser = $request->request->get("from_user");
        $toUser = $request->request->get("to_user");

        $this->service->deleteFollow($fromUser, $toUser);

        return $this->returnOk();
    }

}