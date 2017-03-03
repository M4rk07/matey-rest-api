<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 16.11
 */

namespace App\Handlers\Post;


interface PostHandlerFactoryInterface
{
    /**
     * @param string $type type if registration handler
     * @return PostHandlerInterface
     */
    public function getPostHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getPostHandlers();
}