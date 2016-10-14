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

        $posts = $this->service->getActivityIds($user_id, $start, $count);

        $fullPosts = $this->service->getActivities($posts, count($posts));

        $i =0;
        foreach($fullPosts as $post) {
            $fullPosts[$i]['statistics'] = $this->service->getStatistics($fullPosts[$i]['activity_type'], $post['source_id']);
            if($fullPosts[$i]['activity_type'] == "POST")
                $fullPosts[$i]['last_users_respond'] = $this->service->getLastUsersRespond($post['parent_id']);
            $fullPosts[$i]['data'] = unserialize($fullPosts[$i]['srl_data']);
            unset($fullPosts[$i]['srl_data']);
            $i++;
        }

        return $this->returnOk($fullPosts);

    }

}