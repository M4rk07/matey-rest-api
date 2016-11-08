<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.11.16.
 * Time: 00.42
 */

namespace App\Exception;


use AuthBucket\OAuth2\Exception\ExceptionInterface;

class AlreadyRegisteredException extends \InvalidArgumentException  implements ExceptionInterface
{

    public function __construct($mergeOffer = false, $message = [], $code = 409, \Exception $previous = null)
    {
        if($mergeOffer == true) {
            $message['error'] = 'merge_offer';
        } else {
            $message['error'] = 'full_reg';
            $message['error_description'] = 'Hey Mate, you are already with us!';
        }
        parent::__construct(serialize($message), $code, $previous);
    }

}