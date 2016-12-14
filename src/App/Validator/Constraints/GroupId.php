<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.12.16.
 * Time: 23.31
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;

class GroupId extends Constraint
{
    public $message = "This is not valid group id.";
}