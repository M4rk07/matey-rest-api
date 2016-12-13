<?php

namespace App\Handlers\Account;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.11.16.
 * Time: 16.37
 */
interface AccountHandlerInterface
{

    function createAccount(Request $request);

    function mergeAccount(Request $request);

}