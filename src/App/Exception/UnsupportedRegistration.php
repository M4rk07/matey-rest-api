<?php

namespace Matey\Exception;
use AuthBucket\OAuth2\Exception\ExceptionInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 16.08
 */
class UnsupportedRegistration extends \InvalidArgumentException  implements ExceptionInterface
{

    public function __construct($message = [], $code = 400, \Exception $previous = null)
    {
        $message['error'] = 'unsupported_registration_type';
        parent::__construct(serialize($message), $code, $previous);
    }

}