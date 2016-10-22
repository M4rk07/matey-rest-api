<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 5:06 PM
 */

namespace App\OAuth2Models;


use AuthBucket\OAuth2\Model\ScopeManagerInterface;

class ScopeManager extends AbstractManager implements ScopeManagerInterface
{
    public function __construct () {
        parent::__construct(self::T_A_SCOPES, 'App\\OAuth2Models\\Scope', "scope");
    }
}