<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:51 PM
 */

namespace App\OAuth2Models;


use AuthBucket\OAuth2\Model\CodeManagerInterface;

class CodeManager extends AbstractManager implements CodeManagerInterface
{
    public function __construct () {
        parent::__construct();
        $this->tableName = "oauth2_codes";
        $this->className = 'App\\OAuth2Models\\Code';
    }
}