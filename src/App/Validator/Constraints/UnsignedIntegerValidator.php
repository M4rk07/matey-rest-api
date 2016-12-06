<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.12.16.
 * Time: 15.38
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UnsignedIntegerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if(empty($value) || (int)$value != $value || (int)$value <= 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}