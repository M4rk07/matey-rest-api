<?php

namespace App\Security;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 19.09
 */
class SaltGenerator
{

    public function generateSalt() {

        $length= rand(15, 20);
        return base64_encode(mcrypt_create_iv(ceil(0.75*$length), MCRYPT_DEV_URANDOM));

    }

}