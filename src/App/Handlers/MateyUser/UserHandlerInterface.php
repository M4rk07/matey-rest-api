<?php
namespace App\Handlers\MateyUser;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 30.11.16.
 * Time: 02.00
 */
interface UserHandlerInterface
{
    public function handleGetUser(Application $app, Request $request, $id);
    public function handleFollow(Application $app, Request $request, $id);
    public function handleGetConnections(Application $app, Request $request, $id, $type);
}