<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.10.16.
 * Time: 01.08
 */

namespace App\Controllers;


use App\Services\BaseService;
use App\Services\Redis\RedisService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractController
{

    protected $service;
    protected $redisService;
    protected $validator;

    const BASE_OAuth2_URL = "http://localhost/matey-oauth2/web/index.php";

    public function __construct(
        BaseService $service,
        RedisService $redisService,
        ValidatorInterface $validator
    )
    {
        $this->service = $service;
        $this->redisService = $redisService;
        $this->validator = $validator;
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

    public function returnOk (array $parameters = array()) {

        return new JsonResponse($parameters, 200);

    }

    public function validate ($value, array $classes) {
        $errors = $this->validator->validate($value, $classes);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }
    }

}