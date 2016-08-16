<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 1:14 AM
 */

namespace Matey\OAuth2\Models;

use App\Services\BaseService;

class AbstractManager extends BaseService
{
    protected $tableName;

    public function getClassName()
    {
        return get_class($this);
    }

    public function readModelAll()
    {
        $this->db->fetchAll("SELECT * FROM " . $this->tableName);
    }

}