<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.10.16.
 * Time: 16.16
 */

namespace App\Controllers;


use Symfony\Component\HttpFoundation\Request;

class NewsFeedController extends AbstractController
{

    public function getNewsFeedAction(Request $request, $user_id) {

        $start = $request->get("start");
        $count = $request->get("count");

        $posts = $this->getPostIds($user_id, $start, $count);

        $fullPosts = $this->service->getPosts($posts, count($posts));

        foreach($fullPosts as $post) {
            $fullPosts[$post['source_id']]['statistics'] = $this->service->getStatistics($post['source_id']);
            $fullPosts[$post['source_id']]['last_users_respond'] = $this->service->getLastUsersRespond($post['source_id']);
        }

        return $this->returnOk($fullPosts);

    }

}