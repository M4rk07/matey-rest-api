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
    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Rereply';
    }

    public function getTableName() {
        return self::T_REREPLY;
    }

    public function getKeyName()
    {
        return "REREPLY";
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
            self::FIELD_NUM_OF_APPROVES => 0,
        ));
    }

    public function getStatisticsData (Post $post) {

        $statistics = $this->redis->hgetall($this->getKeyName().":statistics:".$post->getId());

        $post->setValuesFromArray($statistics);

        return $post;

    }

    public function incrNumOfApproves(Rereply $rereply, $incrBy = 1) {
        $this->redis->hincrby($this->getKeyName().":statistics:".$rereply->getId(),
            self::FIELD_NUM_OF_APPROVES, $incrBy);
    }

}