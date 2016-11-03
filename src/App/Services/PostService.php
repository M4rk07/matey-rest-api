<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.22
 */

namespace App\Services;


use App\MateyModels\Response;
use App\MateyModels\User;

class PostService extends ActivityService
{

    public function approve(User $user, Response $response) {

        $result = $this->db->executeUpdate("INSERT IGNORE INTO ".self::T_APPROVE." (user_id, response_id) VALUES(?,?)",
            array($user->getUserId(), $response->getResponseId()));
        if($result <= 0) return false;
        return true;

    }

}