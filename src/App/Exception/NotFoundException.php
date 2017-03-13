<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.12.16.
 * Time: 00.26
 */

namespace App\Exception;


use AuthBucket\OAuth2\Exception\ExceptionInterface;
use AuthBucket\OAuth2\Exception\InvalidRequestException;

class NotFoundException extends \InvalidArgumentException implements ExceptionInterface
{

    public function __construct($mergeOffer = false, $message = [], $code = 404, \Exception $previous = null)
    {

        $message['error'] = 'not_found';
        $message['description'] = 'Resource can not be found.';

        parent::__construct(serialize($message), $code, $previous);
    }

}