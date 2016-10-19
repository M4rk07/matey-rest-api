<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.10.16.
 * Time: 16.16
 */

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class NewsFeedController extends AbstractController
{

    public function getNewsFeedAction(Request $request, $user_id) {

        $start = $request->get("start");
        $count = $request->get("count");

        $this->validate($user_id, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid user_id.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($start, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid start.',
                'type' => 'numeric'
            ))
        ]);
        $this->validate($count, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid count.',
                'type' => 'numeric'
            ))
        ]);

        $posts = $this->service->getActivityIds($user_id, $start, $count);

        $fullPosts = $this->service->getActivities($posts, count($posts));

        $i =0;
        foreach($fullPosts as $post) {

            $fullPosts[$i]['data'] = unserialize($fullPosts[$i]['srl_data']);
            $fullPosts[$i]['data']['statistics'] = $this->service->getStatistics($fullPosts[$i]['activity_type'], $post['source_id']);
            if($fullPosts[$i]['activity_type'] == "POST")
                $fullPosts[$i]['data']['last_users_respond'] = $this->service->getLastUsersRespond($post['source_id']);

            unset($fullPosts[$i]['srl_data']);
            $i++;
        }

        return $this->returnOk($fullPosts);

    }

}