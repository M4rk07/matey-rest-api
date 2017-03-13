<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.3.17.
 * Time: 01.40
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;

class GCM extends Constraint
{
    public $message = "This is not valid GCM.";
}