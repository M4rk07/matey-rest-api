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

    // ACTIVITY TYPES
    const TYPE_USER = "USER";
    const TYPE_INTEREST = "INTEREST";
    const TYPE_POST = "POST";
    const TYPE_FOLLOW = "FOLLOW";
    const TYPE_RESPONSE = "RESPONSE";

    // REDIS KEYS
    const REDIS_NEWSFEED = "USER:newsfeed:";
    const REDIS_STATISTICS_POST = "POST:statistics:";
    const REDIS_STATISTICS_USER = "USER:statistics:";
    const REDIS_STATISTICS_RESPONSE = "RESPONSE:statistics:";
    const REDIS_POST_CHECKER_SET = "USER:posts-checker-set:";
    const REDIS_LAST_3_RESPONSES = "POST:last-3-reponses:";

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../../resources/config/dbal_conn.php';
        $this->redis = new Client(array(
            "persistent" => "1"
        ));
    }

}
