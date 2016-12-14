<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.12.16.
 * Time: 22.52
 */

namespace App\Validators;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GroupPrivacyValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if( empty($value) || ($value != 'PRIVATE' && $value != 'PUBLIC') ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }


}