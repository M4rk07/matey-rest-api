<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 7.11.16.
 * Time: 22.31
 */

namespace App\MateyModels;


interface ModelManagerRedisInterface
{
    public function getKeyName();

    public function getClassName();

    public function startTransaction();

    public function commitTransaction();

    public function rollbackTransaction();
}