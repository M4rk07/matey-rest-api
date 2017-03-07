<?php
namespace App\Algos\FeedRank;
use App\Constants\Defaults\DefaultDates;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.3.17.
 * Time: 14.50
 */
class FeedRank
{

    public function score (\DateTime $timeCreated, $numOfBoosts = 0) {
        $timeCreated = $timeCreated->getTimestamp();
        $now = new \DateTime(DefaultDates::DATE_FORMAT);
        $now = $now->getTimestamp();

        if($numOfBoosts == 0) $numOfBoosts = 1;

        return (1/($now-$timeCreated))*$numOfBoosts;
    }

}