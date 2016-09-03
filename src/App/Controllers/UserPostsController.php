<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 9/3/2016
 * Time: 5:08 PM
 */

namespace App\Controllers;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserPostsController
{

    protected $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function fetchNewsFeedPosts (Application $app, $id_user_requesting) {

        $fetchArray = $this->service->fetchNewsFeedPosts ($id_user_requesting);
        $result = [];

        foreach($fetchArray as $ftcArray1) {

            $post = array(
                "post_id" => $ftcArray1['post_id'],
                "user_posted_id" => $ftcArray1['user_posted_id'],
                "user_posted_first_name" => $ftcArray1['user_posted_first_name'],
                "user_posted_last_name" => $ftcArray1['user_posted_last_name'],
                "post_text" => $ftcArray1['post_text'],
                "post_date" => $ftcArray1['post_date'],
                "replies" => [],
                "interests" => []
                );

            foreach($fetchArray as $ftcArray2) {
                if ($ftcArray2['post_id'] == $post['post_id']) {

                    if(!empty($ftcArray2['reply_id'])) {
                        $post_reply = array(
                            "reply_id" => $ftcArray2['reply_id'],
                            "user_replied_first_name" => $ftcArray2['user_replied_first_name'],
                            "user_replied_last_name" => $ftcArray2['user_replied_last_name'],
                            "reply_user_id" => $ftcArray2['reply_user_id'],
                            "reply_text" => $ftcArray2['reply_text'],
                            "reply_date" => $ftcArray2['reply_date'],
                            "reply_approves" => []
                        );

                        foreach($fetchArray as $ftcArray3) {
                            if (!empty($ftcArray3['aprv_user_id']) && $ftcArray3['reply_id'] == $post_reply['reply_id']) {

                                $reply_aprv = array(
                                    'aprv_user_id' => $ftcArray3['aprv_user_id']
                                );

                                if(!in_array($reply_aprv, $post_reply['reply_approves'], true)) {
                                    $post_reply['reply_approves'][] = $reply_aprv;
                                }

                            }
                        }

                        if (!in_array($post_reply, $post['replies'], true)) {
                            $post['replies'][] = $post_reply;
                        }

                    }

                    if(!empty($ftcArray2['interest_id'])) {
                        $post_interest = array(
                            "interests_id" => $ftcArray2['interest_id'],
                            "post_interest" => $ftcArray2['post_interest']
                        );

                        if (!in_array($post_interest, $post['interests'], true)) {
                            $post['interests'][] = $post_interest;
                        }
                    }

                }
            }

            if(!in_array($post, $result, true)) {
                $result[] = $post;
            }

        }

        return $app->json($result, 200);

    }


}