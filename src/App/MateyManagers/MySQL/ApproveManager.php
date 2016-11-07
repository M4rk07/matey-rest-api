<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 19.26
 */

namespace App\MateyModels;


class ApproveManager extends AbstractManager
{

    public function __construct ($db) {
        parent::__construct($db);
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Approve';
    }

    public function getTableName() {
        return self::T_APPROVE;
    }

}