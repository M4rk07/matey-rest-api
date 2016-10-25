<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.16.
 * Time: 15.00
 */

namespace App\Controllers;


use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class InterestController extends AbstractController
{
    const MIN_SUBINTEREST_SCORE = 1;
    const MAX_SUBINTEREST_SCORE = 5;

    public function addSubinterestAction(Request $request) {

        $user_id = $request->request->get("user_id");
        $subinterests = $request->getContent();
        $subinterests = json_decode($subinterests);

        if(!is_array($subinterests))
            throw new InvalidRequestException(array('error_description' => 'Given value is not an array.'));

        /*
         * Validating received data
         */
        foreach($subinterests as $subinterest) {
            $this->validateNumericUnsigned($subinterest->subinterest_id);
            $this->validateNumericUnsigned($subinterest->score);
            $this->validate($subinterest->score, [
                new Range(array(
                    'min' => self::MIN_SUBINTEREST_SCORE,
                    'max' => self::MAX_SUBINTEREST_SCORE
                )),
            ]);
        }

        /*
         * Storing data about user subinterests
         */
        foreach($subinterests as $subinterest) {

            $this->service->createUserSubinterest($user_id, $subinterest->subinterest_id, $subinterest->score);
            $this->redisService->pushSubinterest($user_id, $subinterest->subinterest_id, $subinterest->score);

        }

        return $this->returnOk();
    }

}