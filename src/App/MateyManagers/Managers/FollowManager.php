<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 15.00
 */

namespace App\MateyModels;


use App\Algos\ActivityWeights;
use App\Algos\Timer;
use App\Constants\Defaults\DefaultDates;
use App\MateyModels\Follow;
use App\MateyModels\User;
use App\Services\BaseService;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelInterface;

class FollowManager extends AbstractManager
{
    const FIELD_NUM_OF_INTERACTIONS = "num_of_interactions";
    const FIELD_SUM_OF_INTERACTIONS = "sum_of_interactions";
    const FIELD_LAST_INTERACTION = "last_interaction";


    public function createModel(ModelInterface $model) {
        $model = parent::createModel($model);

        $this->initializeInteractionsData($model);

        return $model;
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, array $fields = null) {

        $models = parent::readModelBy($criteria, $orderBy, $limit, $offset, $fields);

        if($fields == null || count(array_intersect($this->getRedisFields(), $fields)) && !empty($models)) {
            foreach($models as $key => $model) {
                $models[$key] = $this->getInteractionsData($model);
            }
        }

        return $models;

    }

    public function getRelevantFollowers (Post $post) {
        if(empty($post->getUserId())) throw new ServerErrorException();

        $sql = "SELECT user_id FROM " . $this->getTableName() . " WHERE (parent_id=? AND parent_type=?)";
        if(!empty($post->getGroupId())) {
            $sql .= " OR (parent_id=? AND parent_type=?)";
            $all = $this->db->fetchAll($sql, array($post->getUserId(), Activity::USER_TYPE, $post->getGroupId(), Activity::GROUP_TYPE));
        } else {
            $all = $this->db->fetchAll($sql, array($post->getUserId(), Activity::USER_TYPE));
        }

        $models = $this->makeObjects($all);
        return $models;
    }

    // ---------------------------- REDIS TOOOLS ---------------------------------

    public function initializeInteractionsData(Follow $follow) {
        $now = new \DateTime();
        $now = $now->format(DefaultDates::DATE_FORMAT);

        $this->redis->hmset($this->getRedisKey().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType(), array(
            self::FIELD_NUM_OF_INTERACTIONS => 0,
            self::FIELD_SUM_OF_INTERACTIONS => 0,
            self::FIELD_LAST_INTERACTION => $now
        ));
    }

    public function getInteractionsData (Follow $follow) {

        $statistics = $this->redis->hgetall($this->getRedisKey().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType());

        $follow->setValuesFromArray($statistics);

        return $follow;

    }

    public function incrNumOfInteractions(Follow $follow, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType(),
            self::FIELD_NUM_OF_INTERACTIONS, $incrBy);
    }

    public function incrSumOfInteractions(Follow $follow, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType(),
            self::FIELD_SUM_OF_INTERACTIONS, $incrBy);
    }

    public function updateLastInteraction(Follow $follow) {
        $now = new \DateTime();
        $now = $now->format(DefaultDates::DATE_FORMAT);

        $this->redis->hset($this->getRedisKey().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType(),
            self::FIELD_LAST_INTERACTION, $now);
    }

}