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


    public function __construct()
    {
        $this->db = require __DIR__ . '/../../../resources/config/dbal_conn.php';
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
