<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 16.01
 */

namespace App\Handlers\Registration;


use App\MateyModels\ModelManagerFactoryInterface;
use App\MateyModels\UserManager;
use App\MateyModels\UserManagerRedis;
use Doctrine\DBAL\Connection;
use Matey\Exception\UnsupportedRegistration;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationHandlerFactory implements RegistrationHandlerFactoryInterface
{
    protected $classes;
    protected $validator;
    protected $modelManagerFactory;
    protected $dbConnection;

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
                throw new UnsupportedRegistration([
                    'error_description' => 'The registration type is unsupported.',
                ]);
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('App\\Handlers\\Registration\\RegistrationHandlerInterface')) {
                throw new UnsupportedRegistration([
                    'error_description' => 'The registration type is unsupported.',
                ]);
            }
        }

        $this->classes = $classes;
    }

    public function getRegistrationHandler($type = null)
    {
        $type = $type ?: current(array_keys($this->classes));

        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new UnsupportedRegistration([
                'error_description' => 'The authorization grant type is not supported by the authorization server.',
            ]);
        }

        $class = $this->classes[$type];

        return new $class(
            $this->validator,
            $this->modelManagerFactory,
            $this->dbConnection
        );
    }

    public function getRegistrationHandlers()
    {
        return $this->classes;
    }


}