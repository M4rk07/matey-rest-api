<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 16.03
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraints\Regex;

class DeviceId extends Regex
{
    public function __construct($options = null)
    {
        return parent::__construct(array_merge([
            'message' => 'This is not a valid device_id.',
            'pattern' => '/^([1-9][0-9]*)$/',
        ], (array) $options));
    }

}