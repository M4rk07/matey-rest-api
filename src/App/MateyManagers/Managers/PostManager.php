<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 12.56
 */

namespace App\MateyModels;


use App\MateyModels\Post;
use App\MateyModels\User;
use App\Services\BaseService;
use AuthBucket\OAuth2\Model\ModelInterface;

class PostManager extends AbstractManager
{

    const FIELD_NUM_OF_BOOSTS = "num_of_boosts";
    const FIELD_NUM_OF_REPLIES = "num_of_replies";
    const FIELD_NUM_OF_SHARES = "num_of_shares";
    const FIELD_NUM_OF_BOOKMARKS = "num_of_bookmarks";


    public function createModel(ModelInterface $model) {
        $model = parent::createModel($model);

        $this->initializeStatisticsData($model);

        return $model;
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, array $fields = null) {

        if(!isset($criteria['deleted'])) $criteria['deleted'] = 0;
        if(!isset($criteria['archived'])) $criteria['archived'] = 0;

        $models = parent::readModelBy($criteria, $orderBy, $limit, $offset, $fields);

        if($fields == null || count(array_intersect($this->getRedisFields(), $fields)) && !empty($models)) {
            foreach($models as $key => $model) {
                $models[$key] = $this->getStatisticsData($model);
            }
        }

        return $models;

    }

    // ---------------------------- REDIS TOOOLS ---------------------------------

    public function initializeStatisticsData(Post $post) {
        $this->redis->hmset($this->getRedisKey().":statistics:".$post->getPostId(), array(
            self::FIELD_NUM_OF_BOOSTS => 0,
            self::FIELD_NUM_OF_REPLIES => 0,
            self::FIELD_NUM_OF_SHARES => 0,
            self::FIELD_NUM_OF_BOOKMARKS => 0
        ));
    }

    public function getStatisticsData (Post $post) {

        $statistics = $this->redis->hgetall($this->getRedisKey().":statistics:".$post->getPostId());

        $post->setValuesFromArray($statistics);

        return $post;

    }
    
    public function incrNumOfBoosts(Post $post, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$post->getPostId(),
            self::FIELD_NUM_OF_BOOSTS, $incrBy);
    }

    public function incrNumOfReplies(Post $post, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$post->getPostId(),
            self::FIELD_NUM_OF_REPLIES, $incrBy);
    }

    public function incrNumOfShares(Post $post, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$post->getPostId(),
            self::FIELD_NUM_OF_SHARES, $incrBy);
    }

    public function incrNumOfBookmarks(Post $post, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$post->getPostId(),
            self::FIELD_NUM_OF_BOOKMARKS, $incrBy);
    }

}