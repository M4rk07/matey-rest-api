<?php

namespace App\Handlers\Registration;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 15.55
 */
interface RegistrationHandlerFactoryInterface
{
    /**
     * @param string $type type if registration handler
     * @return RegistrationHandlerInterface
     */
    public function getRegistrationHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getRegistrationHandlers();

}