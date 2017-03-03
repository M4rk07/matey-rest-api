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
use App\MateyModels\Follow;
use App\MateyModels\User;
use App\Services\BaseService;
use AuthBucket\OAuth2\Model\ModelInterface;

class FollowManager extends AbstractManager
{
    const FIELD_NUM_OF_INTERACTIONS = "num_of_interactions";
    const FIELD_SUM_OF_INTERACTIONS = "sum_of_interactions";

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Follow';
    }

    public function getTableName() {
        return self::T_FOLLOWER;
    }

    public function getKeyName()
    {
        return "FOLLOW";
    }

    public function createModel(ModelInterface $model, $ignore = false) {
        $model = parent::createModel($model, $ignore);

        $this->initializeInteractionsData($model);

        return $model;
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, array $fields = null) {

        $models = parent::readModelBy($criteria, $orderBy, $limit, $offset, $fields);

        if($fields == null || (in_array('interactions', $fields) && !empty($models))) {

            foreach($models as $key => $model) {
                $models[$key] = $this->getInteractionsData($model);
            }

        }

        return $models;

    }

    // ---------------------------- REDIS TOOOLS ---------------------------------

    public function initializeInteractionsData(Follow $follow) {
        $this->redis->hmset($this->getKeyName().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType(), array(
            self::FIELD_NUM_OF_INTERACTIONS => 0,
            self::FIELD_SUM_OF_INTERACTIONS => 0,
        ));
    }

    public function getInteractionsData (Follow $follow) {

        $statistics = $this->redis->hgetall($this->getKeyName().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType());

        $follow->setValuesFromArray($statistics);

        return $follow;

    }

    public function incrNumOfInteractions(Follow $follow, $incrBy = 1) {
        $this->redis->hincrby($this->getKeyName().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType(),
            self::FIELD_NUM_OF_INTERACTIONS, $incrBy);
    }

    public function incrSumOfInteractions(Follow $follow, $incrBy = 1) {
        $this->redis->hincrby($this->getKeyName().":interactions:"
            .$follow->getUserId().":".$follow->getParentId().":".$follow->getParentType(),
            self::FIELD_SUM_OF_INTERACTIONS, $incrBy);
    }

}