<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.11.16.
 * Time: 10.29
 */

namespace App\Handlers\Device;


use App\Exception\UnsupportedRegistration;
use App\MateyModels\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeviceHandlerFactory implements DeviceHandlerFactoryInterface
{

    protected $classes;
    protected $validator;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        \AuthBucket\OAuth2\Model\ModelManagerFactoryInterface $modelManagerFactory,
        array $classes = []
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;

        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new ServerErrorException();
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('App\\Handlers\\Device\\DeviceHandlerInterface')) {
                throw new ServerErrorException();
            }
        }

        $this->classes = $classes;
    }

    public function getDeviceHandler($type = null)
    {
        $type = $type ?: current(array_keys($this->classes));

        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new ServerErrorException();
        }

        $class = $this->classes[$type];

        return new $class(
            $this->validator,
            $this->modelManagerFactory
        );
    }

    public function getDeviceHandlers()
    {
        return $this->classes;
    }

}