<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.16.
 * Time: 15.00
 */

namespace App\Controllers;


use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class InterestController extends AbstractController
{
    const MIN_DEPTH = 0;
    const MAX_DEPTH = 3;

    public function addInterestsAction(Request $request) {

        $user_id = $request->request->get("user_id");
        $interests = $request->getContent();
        $interests = json_decode($interests);

        if(!is_array($interests))
            throw new InvalidRequestException(array('error_description' => 'Given value is not an array.'));

        /*
         * Validating received data
         */
        foreach($interests as $interest) {

            $this->validateNumericUnsigned($interest->interest_id);
            $this->validate($interest->depth, [
                new Range(array(
                    'min' => self::MIN_DEPTH,
                    'max' => self::MAX_DEPTH
                )),
            ]);
        }


        /*
         * Storing data about user interest
         */
        $this->storeInterests($user_id, $interests);

        return $this->returnOk();
    }

    public function storeInterests ($user_id, $interests) {

        foreach($interests as $interest) {

            $this->service->createUserInterest($user_id, $interest->interest_id, $interest->depth);

            if ($interest->depth == 0) {
                $this->redisService->pushInterestDepth0($user_id, $interest->interest_id, 3);
            } else if ($interest->depth == 1) {
                $result = $this->service->findParentDepth_1($interest->interest_id);
                if (empty($result['interest_0_id']))
                    throw new InvalidRequestException();
                $this->redisService->pushInterestDepth1($user_id, $result['interest_0_id'], $interest->interest_id, 3);
            } else if ($interest->depth == 2) {
                $result = $this->service->findParentDepth_2($interest->interest_id);
                if (empty($result['interest_0_id']) || empty($result['interest_1_id']))
                    throw new InvalidRequestException();
                $this->redisService->pushInterestDepth2($user_id, $result['interest_0_id'], $result['interest_1_id'], $interest->interest_id, 3);
            } else if ($interest->depth == 3) {
                $result = $this->service->findParentDepth_3($interest->interest_id);
                if (empty($result['interest_0_id']) || empty($result['interest_1_id']) || empty($result['interest_2_id']))
                    throw new InvalidRequestException();
                $this->redisService->pushInterestDepth3($user_id, $result['interest_0_id'], $result['interest_1_id'], $result['interest_2_id'], $interest->interest_id, 3);
            }

        }

    }

    public function showInterestsAction(Request $request) {

        $user_id = $request->request->get('user_id');
        return $this->returnOk($this->redisService->getInterests($user_id));

    }

}