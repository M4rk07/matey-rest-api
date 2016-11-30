<?php
namespace App\Handlers\MateyUser;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 30.11.16.
 * Time: 02.00
 */
interface UserHandlerInterface
{
    public function getUser(Request $request, $id);
}