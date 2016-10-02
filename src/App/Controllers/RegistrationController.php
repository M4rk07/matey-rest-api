<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/17/2016
 * Time: 9:09 PM
 */

namespace App\Controllers;

use App\Handler\ClientRegistrationHandler;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController
{

    protected $clientRegistrationService;

    public function __construct()
    {
    }

    public function clientRegistration (Request $request) {

        return (new ClientRegistrationHandler())->handle($request);

    }

}