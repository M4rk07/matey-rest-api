<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.3.17.
 * Time: 19.47
 */

namespace App\Handlers;


use App\MateyModels\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractHandler
{

    protected $validator;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function validateValue ($value, array $constraints) {
        $errors = $this->validator->validate($value, $constraints);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }
    }

}