<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/17/2016
 * Time: 9:09 PM
 */

namespace App\Controllers;


use Symfony\Component\HttpFoundation\Request;

class RegistrationController
{

    protected $registrationService;

    public function __construct($service)
    {
        $this->registrationService = $service;
    }

    public function registerClient(Request $request) {

    }

}