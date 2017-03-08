<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.55
 */

namespace App\MateyModels;


use App\MateyModels\Response;
use App\MateyModels\User;
use App\Services\BaseService;
use AuthBucket\OAuth2\Model\ModelInterface;

class ReplyManager extends AbstractManager
{

    const FIELD_NUM_OF_APPROVES = "num_of_approves";
    const FILED_NUM_OF_REPLIES = "num_of_replies";

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

    public function initializeStatisticsData(Reply $reply) {
        $this->redis->hmset($this->getRedisKey().":statistics:".$reply->getReplyId(), array(
            self::FIELD_NUM_OF_APPROVES => 0,
            self::FILED_NUM_OF_REPLIES => 0,
        ));
    }

    public function getStatisticsData (Reply $reply) {

        $statistics = $this->redis->hgetall($this->getRedisKey().":statistics:".$reply->getReplyId());

        $reply->setValuesFromArray($statistics);

        return $reply;

    }

    public function incrNumOfApproves(Reply $reply, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$reply->getReplyId(),
            self::FIELD_NUM_OF_APPROVES, $incrBy);
    }

    public function incrNumOfReplies(Reply $reply, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$reply->getReplyId(),
            self::FILED_NUM_OF_REPLIES, $incrBy);
    }

}