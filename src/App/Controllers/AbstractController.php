<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.10.16.
 * Time: 01.08
 */

namespace App\Controllers;


use App\Paths\Paths;
use App\Services\BaseService;
use App\Services\Redis\RedisService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/*
 * Controller that is used as main controller for every other
 */

abstract class AbstractController
{

    protected $service;
    protected $redisService;
    protected $validator;

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

    public function returnTime() {
        $time = new \DateTime();
        return $time->format('Y-m-d H:i:s');
    }

    public function returnNotOk ($message = null) {

        throw new InvalidRequestException([
            'error_description' => ($message==null) ? 'The request includes an invalid parameter value.' : $message,
        ]);

    }

    public function validate ($value, array $classes) {

        $value = $this->clearValue($value);

        $errors = $this->validator->validate($value, $classes);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

    }

    public function validateNumericUnsigned ($value) {
        $this->validate($value, [
            new NotBlank(),
            new Type(array(
                'message' => 'This is not a valid subinterest_id.',
                'type' => 'numeric'
            )),
            new GreaterThan(array(
                'value' => 0
            ))
        ]);
    }

    public function clearValue ($value) {
        return trim($value);
    }

    public function testAuthorization (Request $request) {

        $client = new Client();
        $client->request('POST', Paths::DEBUG_ENDPOINT, [
            'headers' => [
                'Authorization' => 'Bearer 00a9fedbae926eb74625f71685c0161e'
            ],
            'form_params'   => array(
                'access_token' => '00a9fedbae926eb74625f71685c0161e'
            ),
        ]);

        return $this->returnOk();
    }

}