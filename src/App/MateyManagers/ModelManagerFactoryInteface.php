<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 16.48
 */

namespace App\MateyModels;


/**
 * OAuth2 model manager factory interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ModelManagerFactoryInterface
{
    /**
     * Gets a stored model manager.
     *
     * @param string $type Type of model manager.
     *
     * @return ModelManagerFactoryInterface The stored model manager.
     *
     * @throw ServerErrorException If supplied model not found.
     */
    public function getModelManager($type);
}