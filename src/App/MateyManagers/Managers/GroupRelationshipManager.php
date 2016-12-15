<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.12.16.
 * Time: 16.34
 */

namespace App\MateyModels;


class GroupRelationshipManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\GroupRelationship';
    }

    public function getTableName() {
        return self::T_GROUP_RELATIONSHIP;
    }

    public function getKeyName()
    {
        return "GROUP-RELATIONSHIP";
    }

}