<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:39 PM
 */

namespace App\OAuth2Models;

use AuthBucket\OAuth2\Model\ClientManagerInterface;

class ClientManager extends AbstractManager implements ClientManagerInterface
{
    public function __construct () {
        parent::__construct(self::T_A_CLIENTS, 'App\\OAuth2Models\\Client', "client_id");
    }

}