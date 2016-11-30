<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.11.16.
 * Time: 22.26
 */

namespace App\Handlers\Profile;


interface ProfileHandlerFactoryInterface
{

    /**
     * @param string $type type if registration handler
     * @return ProfileHandlerInterface
     */
    public function getProfileHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getProfileHandlers();

}