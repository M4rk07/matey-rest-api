<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.12.16.
 * Time: 15.31
 */

namespace App\Handlers\File;


interface FileHandlerFactoryInterface
{

    /**
     * @param string $type type if registration handler
     * @return FileHandlerInterface
     */
    public function getFileHandler($type = null);

    /**
     * @return array supported registration handler
     */
    public function getFileHandlers();

}