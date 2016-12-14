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

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Group';
    }

    public function getTableName() {
        return self::T_GROUP;
    }

    public function getKeyName()
    {
        return "GROUP";
    }

    public function createModel(ModelInterface $model, $ignore = false)
    {
        $model = parent::createModel($model, $ignore);

        $this->initializeGroupStatistics($model);

        return $model;
    }

    public function initializeGroupStatistics(Group $group) {
        $this->redis->hmset(self::KEY_GROUP.":counts:".$group->getId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0
        ));
    }

}