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

    public function getAccountById($userId);

    public function getAccountByEmail($email);

    public function createAccount(Request $request);

    public function mergeAccount(Request $request);

}