<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 14.21
 */

namespace App\Algos;


class Timer
{

    public static function returnTime() {
        $time = new \DateTime();
        return $time->format('Y-m-d H:i:s');
    }

}