<?php

namespace App\Services;

use Silex\Provider\DoctrineServiceProvider;

class BaseService
{
    protected $db;
    protected $USERS_TABLE = "users";
    protected $USERS_FRIENDS_TABLE = "user_friends";
    protected $USER_POSTS_TABLE = "user_posts";
    protected $USERS_POST_INTEREST_TABLE = "user_post_interests";
    protected $USER_POST_REPLIES_TABLE = "user_post_replies";
    protected $USER_REPLY_APPROVES_TABLE = "user_reply_approves";
    protected $USER_INTERESTS_TABLE = "user_interests";

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../../resources/config/dbal_conn.php';
    }

}
