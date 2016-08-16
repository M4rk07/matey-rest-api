<?php

namespace App\Services;

use Silex\Provider\DoctrineServiceProvider;

class BaseService
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

}
