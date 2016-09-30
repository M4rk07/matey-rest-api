<?php

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.9.16.
 * Time: 23.55
 */
namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DatabaseTesterController
{

    protected $testerService;

    public function __construct($service)
    {
        $this->testerService = $service;
    }

    public function fillUsersTable() {

        $this->testerService->fillUsersTable();



    }

    public function deleteAllUsersTable() {

        $this->testerService->deleteAll();

        return "ok";

    }

}