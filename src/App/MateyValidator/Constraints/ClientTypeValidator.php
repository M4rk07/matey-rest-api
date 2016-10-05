<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 2.10.16.
 * Time: 16.48
 */

namespace Matey\Validator\Constraints;


class ClientTypeValidator
{

    public function validate ($value) {

        if($value !== 'public' && $value !== 'confidential') return false;
        else return true;

    }

}