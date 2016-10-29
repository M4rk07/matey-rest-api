<?php
namespace App\Services\Redis;
use App\Algos\Algo;
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
    const KEY_INTEREST = "INTEREST";

    const SUBKEY_NEWSFEED = "newsfeed";
    const SUBKEY_STATISTICS = "statistics";
    const SUBKEY_POST_CHECKET_SET = "posts-checker-set";
    const SUBKEY_LAST_RESPONSES = "last-responses";
    const SUBKEY_BOOKMARKS = "bookmarks";
    const SUBKEY_USER_ID = "user-id";
    const SUBKEY_CONNECTIONS = "connections";
    const SUBKEY_LOGIN_GCMS = "login-gcms";
    const SUBKEY_INTERESTS = "interests";
    const SUBKEY_SUBINTEREST_STATISTICS = "interest-statistics";
    const SUBKEY_RELATIONSHIP = "relationship";
    const SUBKEY_MUTUAL = "mutual";
    const SUBKEY_DEPTH_0 = "depth-0";
    const SUBKEY_DEPTH_1 = "depth-1";
    const SUBKEY_DEPTH_2 = "depth-2";
    const SUBKEY_DEPTH_3 = "depth-3";
    const SUBKEY_FB_TOKEN = "fb-token";

    const FIELD_NUM_OF_POSTS = "num_of_posts";
    const FIELD_NUM_OF_GIVEN_RESPONSES = "num_of_responses";
    const FIELD_NUM_OF_RECEIVED_APPROVES = "num_of_received_approves";
    const FIELD_NUM_OF_APPROVES = "num_of_approves";
    const FIELD_NUM_OF_GIVEN_APPROVES = "num_of_given_approves";
    const FIELD_NUM_OF_FOLLOWING = "num_of_following";
    const FIELD_NUM_OF_FOLLOWERS = "num_of_followers";
    const FIELD_NUM_OF_SHARES = "num_of_shares";
    const FIELD_NUM_OF_BEST_RESPONSES = "num_of_best_responses";
    const FIELD_NUM_OF_RECEIVED_RESPONSES = "num_of_received_responses";
    const FIELD_NUM_OF_PROFILE_CLICKS = "num_of_profile_clicks";
    const FILED_NUM_OF_SHARES = "num_of_shares";
    const FIELD_SCORE = "score";
    const FIELD_TIME = "time";

    public function __construct()
    {
        $this->redis = new Client(array(
            "persistent" => "1"
        ));
    }

    public function startRedisTransaction() {
        $this->redis->multi();
    }

    public function commitRedisTransaction() {
        $this->redis->exec();
    }

    public function rollbackRedisTransaction() {
        $this->redis->discard();
    }

    // --------------------------------------------------------------------
    // PUSHING FUNCTIONS

    public function pushToNewsFeeds ($activity_id, $activity_time, $user_id) {
        $followerManager = new FollowerService();
        $followers = $followerManager->returnFollowers($user_id);
        $followers[]['from_user'] = $user_id;

        foreach($followers as $follower) {
            $this->pushToFeed($activity_id, $activity_time, $follower['from_user']);
        }
    }

    public function pushActivitiesToOneFeed($activities, $user_id) {

        foreach($activities as $activity) {
            $this->pushToFeed($activity['activity_id'], $activity['activity_time'], $user_id);
        }

    }

    public function pushToFeed($activity_id, $activity_time, $user_id) {
        $algo = new Algo();
        $score = $algo->calculateActivityTimeScore($activity_time);
        $this->redis->zadd(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$user_id, array(
            $activity_id => $score
        ));
        $this->redis->zremrangebyrank(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$user_id, 0, -301);
    }

    public function pushLastResponseToPost ($post_id, $user_id) {
        $this->redis->lpush(self::KEY_POST.":".self::SUBKEY_LAST_RESPONSES.":".$post_id, $user_id);
        $this->redis->ltrim(self::KEY_POST.":".self::SUBKEY_LAST_RESPONSES.":".$post_id, 0, 3);
    }

    public function pushPostBookmark ($post_id, $user_id) {
        $this->redis->sadd(self::KEY_POST.":".self::SUBKEY_BOOKMARKS.":".$post_id, $user_id);
    }

    public function pushNewConnection ($user_id, $followed_user_id) {
        $this->redis->sadd(self::KEY_USER.":".self::SUBKEY_CONNECTIONS.":".$user_id, $followed_user_id);
    }

    public function pushNewLoginGcm ($user_id, $gcm) {
        $this->redis->sadd(self::KEY_USER.":".self::SUBKEY_LOGIN_GCMS.":".$user_id, $gcm);
    }

    public function pushInterestDepth0 ($user_id, $interest_0_id, $incrBy) {
        //$this->redis->incrby(self::KEY_INTEREST.":".self::SUBKEY_DEPTH_0.":".$user_id.":".$interest_0_id, $incrBy);
        //$this->generateScore(self::KEY_INTEREST.":".self::SUBKEY_DEPTH_0.":".$user_id, $interest_0_id);

        $depth = 0;
        $newInterest = $interest_0_id.":".$depth;
        $this->redis->incrby(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id.":".$newInterest, $incrBy);
        $this->generateScore2(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id, $newInterest);

    }

    public function pushInterestDepth1 ($user_id, $interest_0_id, $interest_1_id, $incrBy) {
        //$this->redis->incrby(self::KEY_INTEREST.":".self::SUBKEY_DEPTH_1.":".$user_id.":".$interest_0_id.":".$interest_1_id, $incrBy);
        //$this->generateScore(self::KEY_INTEREST.":".self::SUBKEY_DEPTH_1.":".$user_id.":".$interest_0_id, $interest_1_id);

        $depth = 1;
        $newInterest = $interest_1_id.":".$depth;
        $this->redis->incrby(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id.":".$newInterest, $incrBy);
        $this->generateScore2(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id, $newInterest);

        if($incrBy >= 2)
            $this->pushInterestDepth0($user_id, $interest_0_id, $incrBy-1);
    }

    public function pushInterestDepth2 ($user_id, $interest_0_id=null, $interest_1_id, $interest_2_id, $incrBy) {
        //$this->redis->incrby(self::KEY_INTEREST.":".self::SUBKEY_DEPTH_2.":".$user_id.":".$interest_1_id.":".$interest_2_id, $incrBy);
        //$this->generateScore(self::KEY_INTEREST.":".self::SUBKEY_DEPTH_2.":".$user_id.":".$interest_1_id, $interest_2_id);
        $depth = 2;
        $newInterest = $interest_2_id.":".$depth;
        $this->redis->incrby(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id.":".$newInterest, $incrBy);
        $this->generateScore2(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id, $newInterest);

        if($interest_0_id!=null && $incrBy >= 3)
            $this->pushInterestDepth1($user_id, $interest_0_id, $interest_1_id, $incrBy-1);
    }

    public function pushInterestDepth3 ($user_id, $interest_0_id=null, $interest_1_id=null, $interest_2_id, $interest_3_id, $incrBy) {
        //$this->redis->incrby(self::KEY_INTEREST.":".self::SUBKEY_DEPTH_3.":".$user_id.":".$interest_2_id.":".$interest_3_id, $incrBy);
        //$this->generateScore(self::KEY_INTEREST.":".self::SUBKEY_DEPTH_3.":".$user_id.":".$interest_2_id, $interest_3_id);
        $depth = 3;
        $newInterest = $interest_3_id.":".$depth;
        $this->redis->incrby(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id.":".$newInterest, $incrBy);
        $this->generateScore2(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id, $newInterest);

        if($interest_1_id!=null && $incrBy >= 4)
            $this->pushInterestDepth2($user_id, $interest_0_id, $interest_1_id, $interest_2_id, $incrBy-1);
    }

    public function pushFbAccessToken($user_id, $fb_token) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$user_id, $fb_token);
        $this->redis->expire(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$user_id, 3600);
    }

    // --------------------------------------------------------------------
    // GENERATORS

    public function generateScore2($key, $newInterest) {

        $this->redis->zadd($key, array(
            $newInterest => 1
        ));
        $interests = $this->redis->zrange($key, 0, -1);

        $sum=0;
        $interestsNumbers = [];

        foreach($interests as $interest) {
            $thisNumber = (float)($this->redis->get($key.":".$interest));
            $sum += $thisNumber;
            $intNum['interest'] = $interest;
            $intNum['number'] = $thisNumber;
            $interestsNumbers[] = $intNum;
        }
        foreach($interestsNumbers as $interest) {
            $score = round((float)((float)($interest['number'])/$sum), 3);
            $this->redis->zadd($key, array(
                $interest['interest'] => $score
            ));
        }

        return true;

    }

    public function generateScore($key, $newInterest) {

        $this->redis->zadd($key, array(
            $newInterest => 1
        ));
        $interests = $this->redis->zrange($key, 0, -1);

        $sum=0;
        $interestsNumbers = [];

        foreach($interests as $interest) {
            $thisNumber = (float)($this->redis->get($key.":".$interest));
            $sum += $thisNumber;
            $intNum['interest'] = $interest;
            $intNum['number'] = $thisNumber;
            $interestsNumbers[] = $intNum;
        }
        foreach($interestsNumbers as $interest) {
            $score = round((float)((float)($interest['number'])/$sum), 3);
            $this->redis->zadd($key, array(
                $interest['interest'] => $score
            ));
        }

        return true;

    }

    // --------------------------------------------------------------------
    // GETTERS FUNCTIONS

    public function getIDsFromNewsFeed ($user_id, $start, $count) {
        return $this->redis->zrevrange(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$user_id, $start, $start+$count);
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

    public function getUserIdByEmail ($email) {
        return $this->redis->get(self::KEY_USER.":".self::SUBKEY_USER_ID.":".$email);
    }

    public function getInterests ($user_id) {
        return $this->redis->zrevrange(self::KEY_INTEREST.":".self::SUBKEY_INTERESTS.":".$user_id, 0, -1);
    }

    public function getFbToken ($user_id) {
        return $this->redis->get(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$user_id);
    }

    // --------------------------------------------------------------------
    // INITIALIZING FUNCTIONS

    public function initializeUserStatistics($user_id) {
        $this->redis->hmset(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user_id, array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_FOLLOWING => 0,
            self::FIELD_NUM_OF_POSTS => 0,
            self::FIELD_NUM_OF_GIVEN_APPROVES => 0,
            self::FIELD_NUM_OF_RECEIVED_APPROVES => 0,
            self::FIELD_NUM_OF_GIVEN_RESPONSES => 0,
            self::FIELD_NUM_OF_RECEIVED_RESPONSES => 0,
            self::FIELD_NUM_OF_BEST_RESPONSES => 0,
            self::FIELD_NUM_OF_PROFILE_CLICKS => 0,
            self::FILED_NUM_OF_SHARES => 0
        ));
    }

    public function initializeUserIdByEmail ($email, $user_id) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_USER_ID.":".$email, $user_id);
    }

    public function initializePostStatistics($post_id) {
        $this->redis->hmset(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post_id, array(
            self::FIELD_NUM_OF_GIVEN_RESPONSES => 0,
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
            self::FIELD_NUM_OF_GIVEN_RESPONSES,
            self::FIELD_NUM_OF_SHARES
        ));
    }

    public function deleteResponseStatistics($response_id) {
        $this->redis->hdel(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response_id, array(
            self::FIELD_NUM_OF_APPROVES
        ));
    }

    public function deleteConnection($user_id, $followed_user_id) {
        $this->redis->srem(self::KEY_USER.":".self::SUBKEY_CONNECTIONS.":".$user_id, $followed_user_id);
    }

    public function deleteLoginGcm ($user_id, $gcm) {
        $this->redis->srem(self::KEY_USER.":".self::SUBKEY_LOGIN_GCMS.":".$user_id, $gcm);
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
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user_id, self::FIELD_NUM_OF_GIVEN_RESPONSES, $incrby);
    }

    public function incrPostNumOfResponses($post_id, $incrby) {
        $this->redis->hincrby(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post_id, self::FIELD_NUM_OF_GIVEN_RESPONSES, $incrby);
    }

    public function incrResponseNumOfApproves($response_id, $incrby) {
        $this->redis->hincrby(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response_id, self::FIELD_NUM_OF_APPROVES, $incrby);
    }

    public function incrUserSubinterestStatistic ($user_id, $subinterest_id, $score) {
        $this->redis->zincrby(self::KEY_USER.":".self::SUBKEY_SUBINTEREST_STATISTICS.":".$user_id, $score, $subinterest_id);
    }
    public function incrUserRelationship ($user_from, $user_to, $score, $now_time) {
        $this->redis->hincrby(self::KEY_POST.":".self::SUBKEY_RELATIONSHIP.":".$user_from.":".$user_to,
            self::FIELD_SCORE, $score);
        $this->redis->hset(self::KEY_POST.":".self::SUBKEY_RELATIONSHIP.":".$user_from.":".$user_to,
            self::FIELD_TIME, $now_time);
    }


}