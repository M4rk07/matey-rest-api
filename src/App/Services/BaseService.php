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

    const TYPE_INTEREST = "INTEREST";
    const TYPE_POST = "POST";
    const TYPE_FOLLOW = "FOLLOW";
    const TYPE_RESPONSE = "RESPONSE";

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../../resources/config/dbal_conn.php';
        $this->redis = new Client(array(
            "persistent" => "1"
        ));
    }

    public function pushToNewsFeeds($activity_id, $user_id, $check_id = null) {

        $followerManager = new FollowerService();
        $followers = $followerManager->returnFollowers($user_id);
        $followers[]['from_user'] = $user_id;

        foreach($followers as $follower) {
            if($check_id != null) {
                if ($this->redis->sismember("user:posts_checker_set:".$follower['from_user'], $check_id))
                    continue;
            }

            $this->redis->lpush("newsfeed:".$follower['from_user'], $activity_id);
            $this->redis->ltrim("newsfeed:".$follower['from_user'], 0, 500);
        }

    }

}
