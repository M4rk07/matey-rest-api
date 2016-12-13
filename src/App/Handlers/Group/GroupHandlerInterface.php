<?php

namespace App\Handlers\Group;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.37
 */
interface GroupHandlerInterface
{
    function createGroup(Application $app, Request $request);
    function deleteGroup(Application $app, Request $request);
    function followGroup(Application $app, Request $request);
}