<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.22
 */

namespace App\Services;


class PostService extends NewsFeedGuruService
{

    public function createPost($post_id, $interest_id, $user_id, $text) {

        $this->db->insert(self::T_POST, array(
            'post_id' => $post_id,
            'user_id' => $user_id,
            'text' => $text
        ));
        $activity_id = $this->createPostActivity($post_id, $interest_id, $user_id, $text);

        // UPDATE STATISTICS
        $this->redis->hmset(self::TYPE_POST.":statistics:".$post_id, array(
            "num_of_responses" => 0,
            "num_of_shares" => 0
        ));
        $this->redis->hincrby(self::REDIS_STATISTICS_USER.$user_id, "num_of_posts", 1);
        $this->redis->sadd(self::REDIS_POST_CHECKER_SET.$user_id, $post_id);
        // PUSH TO NEWS FEEDS
        $this->pushToNewsFeeds($activity_id, $user_id);

        return $activity_id;

    }

    public function deletePost($post_id, $user_id) {

        $this->db->delete(self::T_POST, array(
            'post_id' => $post_id
        ));

        // cache new post
        $this->redis->hdel(self::REDIS_STATISTICS_POST.$post_id, array(
            "num_of_responses",
            "num_of_shares"
        ));
        $this->redis->hincrby(self::REDIS_STATISTICS_USER.$user_id, "num_of_posts", -1);

        return $post_id;

    }

    public function createPostActivity($post_id, $interest_id, $user_id, $text) {

        $this->db->insert(self::T_ACTIVITY, array(
            'user_id' => $user_id,
            'source_id' => $post_id,
            'parent_id' => $interest_id,
            'parent_type' => self::TYPE_INTEREST,
            'activity_type' => self::TYPE_POST,
            'srl_data' => serialize(
                array(
                    "post_id" => $post_id,
                    "text" => $text
                )
            )
        ));

        return $this->db->lastInsertId();

    }

    public function deleteActivity($post_id, $parent_type) {

        $this->db->delete(self::T_ACTIVITY, array(
            'source_id' => $post_id,
            'parent_type' => $parent_type
        ));

    }

    public function createResponse($response_id, $user_id, $post_id, $text) {

        $this->db->insert(self::T_RESPONSE, array(
            'response_id' => $response_id,
            'user_id' => $user_id,
            'post_id' => $post_id,
            'text' => $text
        ));
        $activity_id = $this->createResponseActivity($response_id, $user_id, $post_id, $text);

        // PUSH TO NEWS FEEDS
        $this->pushToNewsFeeds($activity_id, $user_id, $post_id);

        // cache new post
        $this->redis->hmset(self::REDIS_STATISTICS_RESPONSE.$response_id, array(
            "num_of_approves" => 0
        ));
        $this->redis->lpush(self::REDIS_LAST_3_RESPONSES.$post_id, $user_id);
        $this->redis->ltrim(self::REDIS_LAST_3_RESPONSES.$post_id, 0, 3);

        $this->redis->hincrby(self::REDIS_STATISTICS_POST.$post_id, "num_of_responses", 1);
        $this->redis->hincrby(self::REDIS_STATISTICS_USER.$user_id, "num_of_responses", 1);

    }

    public function createResponseActivity($response_id, $user_id, $post_id, $text) {

        $this->db->insert(self::T_ACTIVITY, array(
            'user_id' => $user_id,
            'source_id' => $response_id,
            'parent_id' => $post_id,
            'parent_type' => self::TYPE_POST,
            'activity_type' => self::TYPE_RESPONSE,
            'srl_data' => serialize(
                array(
                    "response_id" => $response_id,
                    "text" => $text
                )
            )
        ));

        return $this->db->lastInsertId();

    }

    public function deleteResponse($response_id, $post_id, $user_id) {

        $this->db->delete(self::T_RESPONSE, array(
            'response_id' => $response_id,
            'post_id' => $post_id,
            'user_id' => $user_id
        ));

        $this->redis->hincrby(self::REDIS_STATISTICS_POST.$post_id, "num_of_responses", -1);
        $this->redis->hincrby(self::REDIS_STATISTICS_USER.$user_id, "num_of_responses", -1);
        $this->redis->hdel(self::REDIS_STATISTICS_RESPONSE.$response_id, array(
            "num_of_approves"
        ));

    }

    public function approve($user_id, $response_id) {

        $this->db->insert(self::T_APPROVE, array(
            'response_id' => $response_id,
            'user_id' => $user_id
        ));

        $this->redis->hincrby(self::REDIS_STATISTICS_RESPONSE.$response_id, "num_of_approves", 1);

    }

}