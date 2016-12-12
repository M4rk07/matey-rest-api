<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.12.16.
 * Time: 14.17
 */

namespace App\Handlers\File;


use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractFileHandler implements FileHandlerInterface
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