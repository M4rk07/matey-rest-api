<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.12.16.
 * Time: 22.52
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;

class GroupPrivacy extends Constraint
{
    public $message = 'Not valid privacy value.';
}