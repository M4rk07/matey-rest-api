<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.19
 */

namespace App\MateyModels;


use AuthBucket\OAuth2\Model\ModelInterface;

class RereplyManager extends AbstractManager
{

    const FIELD_NUM_OF_APPROVES = "num_of_approves";

    public function createModel(ModelInterface $model) {
        $model = parent::createModel($model);

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

    public function initializeStatisticsData(Rereply $rereply) {
        $this->redis->hmset($this->getRedisKey().":statistics:".$rereply->getRereplyId(), array(
            self::FIELD_NUM_OF_APPROVES => 0,
        ));
    }

    public function getStatisticsData (Rereply $rereply) {

        $statistics = $this->redis->hgetall($this->getRedisKey().":statistics:".$rereply->getRereplyId());

        $rereply->setValuesFromArray($statistics);

        return $rereply;

    }

    public function incrNumOfApproves(Rereply $rereply, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$rereply->getRereplyId(),
            self::FIELD_NUM_OF_APPROVES, $incrBy);
    }

}