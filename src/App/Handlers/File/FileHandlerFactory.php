<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.12.16.
 * Time: 15.32
 */

namespace App\Handlers\File;


use App\Exception\UnsupportedRegistration;
use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileHandlerFactory implements FileHandlerFactoryInterface
{
    protected $classes;
    protected $validator;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        array $classes = []
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;

        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new UnsupportedRegistration();
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('App\\Handlers\\File\\FileHandlerInterface')) {
                throw new UnsupportedRegistration();
            }
        }

        $this->classes = $classes;
    }

    public function getFileHandler($type = null)
    {
        $type = $type ?: current(array_keys($this->classes));

        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new UnsupportedRegistration();
        }

        $class = $this->classes[$type];

        return new $class(
            $this->validator,
            $this->modelManagerFactory
        );
    }

    public function getFileHandlers()
    {
        return $this->classes;
    }
}