<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 30.11.16.
 * Time: 02.03
 */

namespace App\Handlers\MateyUser;


interface UserHandlerFactoryInterface
{

    /**
     * @param string $type type if registration handler
     * @return UserHandlerInterface
     */
    public function getUserHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getUserHandlers();

}