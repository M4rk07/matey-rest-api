<?php
namespace App\Algos;
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 24.10.16.
 * Time: 16.37
 */
class Algo
{

    public function calculateActivityTimeScore($time) {
        return strtotime($time);
    }

}