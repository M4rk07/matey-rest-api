<?php

namespace App\Handlers\File;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.12.16.
 * Time: 14.16
 */
interface FileHandlerInterface
{

    public function upload(Application $app, Request $request);

}