<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 22.18
 */

namespace App\MateyModels;


use AuthBucket\OAuth2\Model\ModelInterface;

class GroupManager extends AbstractManager
{

    const FIELD_NUM_OF_FOLLOWERS = "num_of_followers";

    public function createModel(ModelInterface $model)
    {
        $model = parent::createModel($model);

        $this->initializeGroupStatistics($model);

        return $model;
    }

    public function initializeGroupStatistics(Group $group) {
        $this->redis->hmset($this->getRedisKey().":counts:".$group->getGroupId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0
        ));
    }

}