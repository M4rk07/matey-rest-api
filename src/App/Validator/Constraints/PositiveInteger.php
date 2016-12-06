<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.12.16.
 * Time: 16.04
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;

class PositiveInteger extends Constraint
{
    public $message = 'Not positive integer number.';
}