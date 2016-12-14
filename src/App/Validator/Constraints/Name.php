<?php


namespace App\Validators;
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 19.10.16.
 * Time: 01.59
 */



use Symfony\Component\Validator\Constraints\Regex;

class Name extends Regex
{
    public function __construct($options = null)
    {
        return parent::__construct(array_merge([
            'message' => 'This is not a valid first_name.',
            'pattern' => '/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.\'-]+$/u',
        ], (array) $options));
    }
}