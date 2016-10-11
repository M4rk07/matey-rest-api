<?php

namespace App\Security;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.10.16.
 * Time: 20.09
 */
class SecretGenerator
{

    public function generateDeviceSecret() {

        return md5(openssl_random_pseudo_bytes(128));

    }

}