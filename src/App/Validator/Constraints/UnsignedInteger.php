<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.12.16.
 * Time: 15.37
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;

class UnsignedInteger extends Constraint
{

    public $message = 'Not unsigned integer number.';

}