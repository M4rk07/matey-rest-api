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
    const FILED_NUM_OF_REPLIES = "num_of_replies";

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Post';
    }

    public function getTableName() {
        return self::T_POST;
    }

    public function getKeyName()
    {
        return "POST";
    }

    public function createModel(ModelInterface $model, $ignore = false) {
        $model = parent::createModel($model, $ignore);

        $this->initializeStatisticsData($model);

        return $model;
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, array $fields = null) {

        $models = parent::readModelBy($criteria, $orderBy, $limit, $offset, $fields);

        if($fields == null || (in_array('statistics', $fields) && !empty($models))) {

            foreach($models as $key => $model) {
                $models[$key] = $this->getStatisticsData($model);
            }

        }

        return $models;

    }

    // ---------------------------- REDIS TOOOLS ---------------------------------

    public function initializeStatisticsData(Post $post) {
        $this->redis->hmset($this->getKeyName().":statistics:".$post->getId(), array(
            self::FIELD_NUM_OF_BOOSTS => 0,
            self::FILED_NUM_OF_REPLIES => 0,
        ));
    }

    public function getStatisticsData (Post $post) {

        $statistics = $this->redis->hgetall($this->getKeyName().":statistics:".$post->getId());

        $post->setValuesFromArray($statistics);

        return $post;

    }

    public function incrNumOfBoosts(Post $post, $incrBy = 1) {
        $this->redis->hincrby($this->getKeyName().":statistics:".$post->getId(),
            self::FIELD_NUM_OF_BOOSTS, $incrBy);
    }

    public function incrNumOfReplies(Post $post, $incrBy = 1) {
        $this->redis->hincrby($this->getKeyName().":statistics:".$post->getId(),
            self::FILED_NUM_OF_REPLIES, $incrBy);
    }

}