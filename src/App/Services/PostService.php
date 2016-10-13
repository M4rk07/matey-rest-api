<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.22
 */

namespace App\Services;


class PostService extends BaseService
{

    public function createPost($post_id, $user_id, $text) {

        $this->db->insert(self::T_POST, array(
            'post_id' => $post_id,
            'user_id' => $user_id,
            'text' => $text
        ));

        // cache new post
        $this->redis->hmset("post:statistics:".$post_id, array(
            "num_of_responses" => 0,
            "num_of_shares" => 0
        ));
        $this->redis->hincrby("user:statistics:".$user_id, "num_of_posts", 1);
        // push to news feed
        $this->pushToNewsFeeds($post_id, $user_id);

        return $post_id;

    }

    public function deletePost($post_id, $user_id) {

        $this->db->delete(self::T_POST, array(
            'post_id' => $post_id
        ));

        // cache new post
        $this->redis->hdel("post:statistics:".$post_id, array(
            "num_of_responses",
            "num_of_shares"
        ));
        $this->redis->hincrby("user:statistics:".$user_id, "num_of_posts", -1);

        return $post_id;

    }

    public function createActivity($user_id, $source_id, $parent_type, $activity_type, $srl_data) {

        $this->db->insert(self::T_ACTIVITY, array(
            'user_id' => $user_id,
            'source_id' => $source_id,
            'parent_type' => $parent_type,
            'activity_type' => $activity_type,
            'srl_data' => $srl_data
        ));

    }

    public function deleteActivity($post_id, $parent_type) {

        $this->db->delete(self::T_ACTIVITY, array(
            'source_id' => $post_id,
            'parent_type' => $parent_type
        ));

    }

    public function pushToNewsFeeds($post_id, $ofUser) {

        $followerManager = new FollowerService();
        $followers = $followerManager->returnFollowers($ofUser);
        $followers[]['from_user'] = $ofUser;

        foreach($followers as $follower) {
            $this->redis->lpush("newsfeed:posts:".$follower['from_user'], $post_id);
            $this->redis->ltrim("newsfeed:posts:".$follower['from_user'], 0, 300);
        }

    }

    public function createResponse($response_id, $user_id, $post_id, $text) {

        $this->db->insert(self::T_RESPONSE, array(
            'response_id' => $response_id,
            'user_id' => $user_id,
            'post_id' => $post_id,
            'text' => $text
        ));

        // cache new post
        $this->redis->hmset("response:statistics:".$response_id, array(
            "num_of_approves" => 0
        ));
        $this->redis->lpush("post:responses:user:".$post_id, $user_id);
        $this->redis->ltrim("post:responses:user:".$post_id, 0, 3);

        $this->redis->lpush("post:responses:".$post_id, $response_id);
        $this->redis->ltrim("post:responses:".$post_id, 0, 10);

    }

    public function deleteResponse($response_id) {

        $this->db->delete(self::T_RESPONSE, array(
            'response_id' => $response_id
        ));

        $this->redis->hdel("response:statistics:".$response_id, array(
            "num_of_approves"
        ));

    }

    public function approve($user_id, $response_id) {

        $this->db->insert(self::T_APPROVE, array(
            'response_id' => $response_id,
            'user_id' => $user_id
        ));

        $this->redis->hincrby("response:statistics:".$response_id, "num_of_approves", 1);

    }

}