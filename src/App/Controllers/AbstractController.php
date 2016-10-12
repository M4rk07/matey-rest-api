<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.10.16.
 * Time: 01.08
 */

namespace App\Controllers;


use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractController
{

    protected $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    public function returnOk () {

        return new JsonResponse(null, 200);

    }

}