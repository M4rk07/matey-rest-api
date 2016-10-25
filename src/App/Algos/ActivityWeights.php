<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.16.
 * Time: 16.31
 */

namespace App\Algos;


class ActivityWeights
{

    const POST_SCORE = 1;
    const RESPONSE_SCORE = 0.9;
    const SHARE_SCORE = 0.8;
    const FOLLOW_SCORE = 0.1;
    const APPROVE_SCORE = 0.1;

}