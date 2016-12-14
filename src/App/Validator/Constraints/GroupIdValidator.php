<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.12.16.
 * Time: 23.32
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GroupIdValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if( (empty($value) && $value != 0) || (int)$value != $value || (int)$value < 0 ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

}