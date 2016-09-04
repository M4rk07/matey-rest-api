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
        // prepare arrays for dumping
        $addedPostsIds = [];

        // go through whole result
        ///////////////////////////
        foreach($fetchArray as $ftcArray1) {

            // check if this post is allready in
            if(in_array($ftcArray1['post_id'], $addedPostsIds, true)) continue;
            $addedPostsIds[] = $ftcArray1['post_id'];

            // make a base post JSON fields
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

            // prepare arrays for dumping
            $addedRepliesIds = [];
            $addedInterestsIds = [];

            // go through whole result pushing replies and interests
            ////////////////////////////////////////
            foreach($fetchArray as $ftcArray2) {
                // check if matches target post and if does
                // take reply and interest
                if (!empty($ftcArray2['post_id']) && $ftcArray2['post_id'] == $post['post_id']) {

                    // checking if there is any reply
                    if(!empty($ftcArray2['reply_id'])) {

                        // if this reply haven't been taken yet pushing it
                        if(!in_array($ftcArray2['reply_id'], $addedRepliesIds, true)) {
                            $addedRepliesIds[] = $ftcArray2['reply_id'];

                            // make array with fields for that reply
                            $post_reply = array(
                                "reply_id" => $ftcArray2['reply_id'],
                                "user_replied_first_name" => $ftcArray2['user_replied_first_name'],
                                "user_replied_last_name" => $ftcArray2['user_replied_last_name'],
                                "reply_user_id" => $ftcArray2['reply_user_id'],
                                "reply_text" => $ftcArray2['reply_text'],
                                "reply_date" => $ftcArray2['reply_date'],
                                "reply_approves" => []
                            );

                            // prepare arrays for dumping
                            $addedApprvs = [];

                            // go throught whole result and search for all approves for target reply
                            /////////////////////////////////////////////////
                            foreach ($fetchArray as $ftcArray3) {

                                // if approve matches target reply
                                if (!empty($ftcArray3['aprv_user_id']) && $ftcArray3['reply_id'] == $post_reply['reply_id']) {

                                    // first checking if it haven't been pushed allready, no duplicates
                                    if (!in_array($ftcArray3['aprv_user_id'], $addedApprvs, true)) {
                                        $addedApprvs[] = $ftcArray3['aprv_user_id'];

                                        // make array with fields for that approve
                                        $reply_aprv = array(
                                            'aprv_user_id' => $ftcArray3['aprv_user_id']
                                        );

                                        // pushing it to reply_approves
                                        $post_reply['reply_approves'][] = $reply_aprv;
                                    }

                                }
                            }

                            // pushing the reply that have just been made to the replies field of post array
                            $post['replies'][] = $post_reply;

                        }

                    }

                    // taking interest if there is one
                    if(!empty($ftcArray2['interest_id'])) {

                        // checking that it haven't been pushed allready
                        if(!in_array($ftcArray2['interest_id'], $addedInterestsIds, true)) {
                            $addedInterestsIds[] = $ftcArray2['interest_id'];

                            // making array for interest with fields
                            $post_interest = array(
                                "interest_id" => $ftcArray2['interest_id'],
                                "interest_name" => $ftcArray2['interest_name']
                            );

                            // pushing interest to interests field of the post
                            $post['interests'][] = $post_interest;

                        }
                    }

                }
            }

            // push that post to the final result
            $result[] = $post;

        }

        // returning result
        return $app->json($result, 200);

    }


}