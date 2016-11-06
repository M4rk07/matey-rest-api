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

    public function __construct () {
        parent::__construct(self::T_APPROVE, 'App\\MateyModels\\Approve');
    }

}