<?php

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 17.45
 */
interface HandlerFactoryInterface
{
    /**
     * @param string $type type if registration handler
     * @return HandlerInterface
     */
    public function getHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getHandlers();
}