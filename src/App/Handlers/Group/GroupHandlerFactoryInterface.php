<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 22.08
 */

namespace App\Handlers\Group;


interface GroupHandlerFactoryInterface
{

    /**
     * @param string $type type if registration handler
     * @return GroupHandlerInterface
     */
    public function getGroupHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getGroupHandlers();

}