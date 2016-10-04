<?php

namespace App\Services;

use Doctrine\DBAL\Connection;
use Mockery\CountValidator\Exception;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseService
{
    // Database connection holder
    protected $db;

    // Database resource table names
    const T_USER = "matey_user";
    const T_STANDARD_USER = "matey_standard_user";
    const T_FB_USER = "matey_fb_user";
    const T_FOLLOWER = "matey_follower";
    const T_POST = "matey_post";
    const T_APPROVE = "matey_approve";
    const T_RESPONSE = "matey_response";
    const T_SHARE = "matey_share";
    const T_ACTIVITY = "matey_activity";
    const T_ACTIVITY_TYPE = "matey_activity_type";
    const T_DEVICE = "matey_device";
    const T_LOGIN = "matey_login";

    // Database authorization table names
    const T_A_ACCESS_TOKEN = "oauth2_access_tokens";
    const T_A_REFRESH_TOKEN = "oauth2_refresh_tokens";
    const T_A_CLIENTS = "oauth2_clients";
    const T_A_CODES = "oauth2_codes";
    const T_A_AUTHORIZE = "oauth2_authorize";
    const T_A_SCOPES = "oauth2_scopes";

    // particular class properties
    protected $tableName;
    protected $className;
    protected $identifier;


    public function __construct()
    {
        $this->db = require __DIR__ . '/../../../resources/config/dbal_conn.php';
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

}
