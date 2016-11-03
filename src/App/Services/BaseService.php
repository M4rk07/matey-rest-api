<?php

namespace App\Services;

use Doctrine\DBAL\Connection;
use Mockery\CountValidator\Exception;
use Predis\Client;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseService
{
    // Database connection holder
    protected $db;
    protected $redis;

    // Database resource table names
    const T_USER = "matey_user";
    const T_FACEBOOK_INFO = "matey_facebook_info";
    const T_FOLLOWER = "matey_follower";
    const T_POST = "matey_post";
    const T_APPROVE = "matey_approve";
    const T_RESPONSE = "matey_response";
    const T_SHARE = "matey_share";
    const T_ACTIVITY = "matey_activity";
    const T_ACTIVITY_TYPE = "matey_activity_type";
    const T_DEVICE = "matey_device";
    const T_LOGIN = "matey_login";
    const T_BOOKMARK = "matey_bookmark";
    const T_USER_INFO = "matey_user_info";
    const T_USER_INTEREST = "matey_user_interest";
    const T_INTEREST_DEPTH_ = "matey_interest_depth_";
    const T_GROUP = "matey_group";

    // Database authorization table names
    const T_A_USER = "oauth2_user";
    const T_A_ACCESS_TOKEN = "oauth2_access_token";
    const T_A_REFRESH_TOKEN = "oauth2_refresh_token";
    const T_A_CLIENTS = "oauth2_client";
    const T_A_CODES = "oauth2_code";
    const T_A_AUTHORIZE = "oauth2_authorize";
    const T_A_SCOPES = "oauth2_scope";

    // ACTIVITY TYPES
    const TYPE_USER = "USER";
    const TYPE_GENERAL = "GENERAL";
    const TYPE_INTEREST = "INTEREST";
    const TYPE_POST = "POST";
    const TYPE_FOLLOW = "FOLLOW";
    const TYPE_RESPONSE = "RESPONSE";
    const TYPE_SHARE = "SHARE";

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
        $this->db = require __DIR__ . '/../../../resources/config/dbal_conn.php';
        $this->redis = new Client(array(
            "persistent" => "1"
        ));
    }

    public function startTransaction() {
        $this->db->beginTransaction();
    }

    public function commitTransaction() {
        $this->db->commit();
    }

    public function rollbackTransaction() {
        $this->db->rollBack();
    }

}
