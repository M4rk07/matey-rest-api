<?php

namespace App\Services;

use Silex\Provider\DoctrineServiceProvider;

class BaseService
{
    protected $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../../resources/config/dbal_conn.php';
    }

}
