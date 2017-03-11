<?php

namespace App\Handlers\Reply;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 17.45
 */
class HandlerFactory implements \HandlerFactoryInterface
{

    protected $classes;
    protected $validator;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        array $classes = [],
        $handlerInterfaceName
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;

        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new ServerErrorException();
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface($handlerInterfaceName)) {
                throw new ServerErrorException();
            }
        }

        $this->classes = $classes;
    }

    public function getHandler($type = null)
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

    public function getHandlers()
    {
        return $this->classes;
    }

}