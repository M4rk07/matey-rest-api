<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 22.18
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultNumbers;
use AuthBucket\OAuth2\Model\ModelInterface;

class GroupManager extends AbstractManager
{

    const FIELD_NUM_OF_FOLLOWERS = "num_of_followers";
    const FIELD_NUM_OF_SHARES = "num_of_shares";
    const FIELD_NUM_OF_FAVORITES = "num_of_favorites";

    public function createModel(ModelInterface $model)
    {
        $model = parent::createModel($model);

        $this->initializeGroupStatistics($model);

        return $model;
    }

    public function initializeGroupStatistics(Group $group) {
        $this->redis->hmset($this->getRedisKey().":statistics:".$group->getGroupId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_SHARES => 0,
            self::FIELD_NUM_OF_SHARES => 0
        ));
    }

    public function pushDeck (Group $group, $posts) {
        if(!is_array($posts)) $posts = array($posts);
        foreach($posts as $post)
            $this->redis->lpush($this->getRedisKey().":feed:".$group->getGroupId(), $post->getPostId());

        $this->redis->ltrim($this->getRedisKey().":deck:".$group->getGroupId(), 0, DefaultNumbers::DECK_CAPACITY);
    }

    public function getDeck (Group $group, $start = 0, $stop = 10) {
        return $this->redis->lrange($this->getRedisKey().":feed:".$group->getGroupId(), $start, $stop);
    }

}