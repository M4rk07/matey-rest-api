<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 10.13
 */

namespace App\Handlers\Device;


use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractDeviceHandler implements DeviceHandlerInterface
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