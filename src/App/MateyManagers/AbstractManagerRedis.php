<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 7.11.16.
 * Time: 22.28
 */

namespace App\MateyModels;


use Predis\Client;

abstract class AbstractManagerRedis implements ModelManagerRedisInterface
{

    protected $redis;

    // REDIS MOTHERFUCKER!
    // REDIS KEYS
    const KEY_APP = "APP";
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
    const FIELD_NUM_OF_GIVEN_RESPONSES = "num_of_given_responses";
    const FIELD_NUM_OF_RECEIVED_APPROVES = "num_of_received_approves";
    const FIELD_NUM_OF_APPROVES = "num_of_given_approves";
    const FIELD_NUM_OF_GIVEN_APPROVES = "num_of_given_approves";
    const FIELD_NUM_OF_FOLLOWING = "num_of_following";
    const FIELD_NUM_OF_FOLLOWERS = "num_of_followers";
    const FIELD_NUM_OF_SHARES = "num_of_shares";
    const FIELD_NUM_OF_BEST_RESPONSES = "num_of_best_responses";
    const FIELD_NUM_OF_RECEIVED_RESPONSES = "num_of_received_responses";
    const FIELD_NUM_OF_PROFILE_CLICKS = "num_of_profile_clicks";
    const FIELD_SCORE = "score";
    const FIELD_TIME = "time";

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    public function startTransaction() {
        $this->redis->multi();
    }

    public function commitTransaction() {
        $this->redis->exec();
    }

    public function rollbackTransaction() {
        $this->redis->discard();
    }

}