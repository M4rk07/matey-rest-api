<?php
namespace App\Services\Redis;
use App\Services\FollowerService;
use Predis\Client;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 20.10.16.
 * Time: 16.03
 */

class RedisService
{

    protected $redis;

    // REDIS KEYS
    const KEY_USER = "USER";
    const KEY_POST = "POST";
    const KEY_RESPONSE = "RESPONSE";

    const SUBKEY_NEWSFEED = "newsfeed";
    const SUBKEY_STATISTICS = "statistics";
    const SUBKEY_POST_CHECKET_SET = "posts-checker-set";
    const SUBKEY_LAST_RESPONSES = "last-responses";

    const FIELD_NUM_OF_POSTS = "num_of_posts";
    const FIELD_NUM_OF_RESPONSES = "num_of_responses";
    const FIELD_NUM_OF_RECEIVED_APPROVES = "num_of_received_approves";
    const FIELD_NUM_OF_APPROVES = "num_of_approves";
    const FIELD_NUM_OF_FOLLOWING = "num_of_following";
    const FIELD_NUM_OF_FOLLOWERS = "num_of_followers";
    const FIELD_NUM_OF_SHARES = "num_of_shares";

    public function __construct()
    {
        $this->redis = new Client(array(
            "persistent" => "1"
        ));
    }

    // --------------------------------------------------------------------
    // PUSHING FUNCTIONS

    public function pushToNewsFeeds ($activity_id, $user_id, $check_id = null) {
        $followerManager = new FollowerService();
        $followers = $followerManager->returnFollowers($user_id);
        $followers[]['from_user'] = $user_id;

        foreach($followers as $follower) {
            $this->redis->lpush(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$follower['from_user'], $activity_id);
            $this->redis->ltrim(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$follower['from_user'], 0, 500);
        }
    }

    public function pushLastResponseToPost ($post_id, $user_id) {
        $this->redis->lpush(self::KEY_POST.":".self::SUBKEY_LAST_RESPONSES.":".$post_id, $user_id);
        $this->redis->ltrim(self::KEY_POST.":".self::SUBKEY_LAST_RESPONSES.":".$post_id, 0, 3);
    }

    // --------------------------------------------------------------------
    // GETTERS FUNCTIONS

    public function getIDsFromNewsFeed ($user_id, $start, $count) {
        return $this->redis->lrange(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$user_id, $start, $start+$count);
    }

    public function getLastUsersRespond($post_id) {
        return $this->redis->lrange(self::KEY_POST.":".self::SUBKEY_LAST_RESPONSES.":".$post_id, 0, -1);
    }

    public function getPostStatistics ($post_id) {
        return $this->redis->hgetall(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post_id);
    }

    public function getResponseStatistics ($response_id) {
        return $this->redis->hgetall(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response_id);
    }

    // --------------------------------------------------------------------
    // INITIALIZING FUNCTIONS

    public function initializeUserStatistics($user_id) {
        $this->redis->hmset(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user_id, array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_FOLLOWING => 0,
            self::FIELD_NUM_OF_POSTS => 0,
            self::FIELD_NUM_OF_RECEIVED_APPROVES => 0,
            self::FIELD_NUM_OF_RESPONSES => 0
        ));
    }

    public function initializePostStatistics($post_id) {
        $this->redis->hmset(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post_id, array(
            self::FIELD_NUM_OF_RESPONSES => 0,
            self::FIELD_NUM_OF_SHARES => 0
        ));
    }

    public function initializeResponseStatistics($response_id) {
        $this->redis->hmset(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response_id, array(
            self::FIELD_NUM_OF_APPROVES => 0
        ));
    }

    // --------------------------------------------------------------------
    // DELETING FUNCTIONS

    public function deletePostStatistics($post_id) {
        $this->redis->hdel(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post_id, array(
            self::FIELD_NUM_OF_RESPONSES,
            self::FIELD_NUM_OF_SHARES
        ));
    }

    public function deleteResponseStatistics($response_id) {
        $this->redis->hdel(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response_id, array(
            self::FIELD_NUM_OF_APPROVES
        ));
    }

    // --------------------------------------------------------------------
    // INCREMENTING FUNCTIONS

    public function incrUserNumOfFollowers($user_id, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user_id, self::FIELD_NUM_OF_FOLLOWERS, $incrby);
    }

    public function incrUserNumOfFollowing($user_id, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user_id, self::FIELD_NUM_OF_FOLLOWING, $incrby);
    }

    public function incrUserNumOfPosts($user_id, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user_id, self::FIELD_NUM_OF_POSTS, $incrby);
    }

    public function incrUserNumOfReceivedApproves($user_id, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user_id, self::FIELD_NUM_OF_RECEIVED_APPROVES, $incrby);
    }

    public function incrUserNumOfResponses($user_id, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user_id, self::FIELD_NUM_OF_RESPONSES, $incrby);
    }

    public function incrPostNumOfResponses($post_id, $incrby) {
        $this->redis->hincrby(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post_id, self::FIELD_NUM_OF_RESPONSES, $incrby);
    }

    public function incrResponseNumOfApproves($response_id, $incrby) {
        $this->redis->hincrby(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response_id, self::FIELD_NUM_OF_APPROVES, $incrby);
    }



}