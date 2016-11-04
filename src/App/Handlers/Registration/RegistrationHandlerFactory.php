<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 16.01
 */

namespace Matey\Handlers\Registration;


use Matey\Exception\UnsupportedRegistration;

class RegistrationHandlerFactory implements RegistrationHandlerFactoryInterface
{
    protected $classes;

    public function __construct(
        array $classes = []
    )
    {
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new UnsupportedRegistration([
                    'error_description' => 'The registration type is unsupported.',
                ]);
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('Matey\\Handlers\\Registration\\RegistrationHandlerInterface')) {
                throw new UnsupportedRegistration([
                    'error_description' => 'The registration type is unsupported.',
                ]);
            }
        }

        $this->classes = $classes;
    }

    public function getGrantTypeHandler($type = null)
    {
        $type = $type ?: current(array_keys($this->classes));

        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new UnsupportedRegistration([
                'error_description' => 'The authorization grant type is not supported by the authorization server.',
            ]);
        }

        $class = $this->classes[$type];

        return new $class;
    }

    public function getGrantTypeHandlers()
    {
        return $this->classes;
    }


}