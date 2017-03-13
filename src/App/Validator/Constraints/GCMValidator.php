<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.3.17.
 * Time: 01.41
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GCMValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if(strlen($value) < 20) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

}