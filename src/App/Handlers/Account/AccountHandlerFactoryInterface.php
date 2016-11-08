<?php

namespace App\Handlers\Account;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.11.16.
 * Time: 16.36
 */
interface AccountHandlerFactoryInterface
{

    /**
     * @param string $type type if registration handler
     * @return AccountHandlerInterface
     */
    public function getAccountHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getAccountHandlers();

}