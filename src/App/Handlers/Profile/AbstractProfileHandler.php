<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.11.16.
 * Time: 22.22
 */

namespace App\Handlers\Profile;


use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractProfileHandler implements ProfileHandlerInterface
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

}