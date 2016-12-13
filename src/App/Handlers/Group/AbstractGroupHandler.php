<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.42
 */

namespace App\Handlers\Group;


use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractGroupHandler implements GroupHandlerInterface
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