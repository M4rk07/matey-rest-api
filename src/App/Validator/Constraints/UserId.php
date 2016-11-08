<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.11.16.
 * Time: 15.49
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraints\Regex;

class UserId extends Regex
{

    public function __construct($options = null)
    {
        return parent::__construct(array_merge([
            'message' => 'This is not a valid user_id.',
            'pattern' => '/^([1-9][0-9]*)$/',
        ], (array) $options));
    }

}