<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.10.16.
 * Time: 16.16
 */

namespace App\Services;


use Symfony\Component\HttpFoundation\Request;

class NewsFeedService extends ActivityService
{

    public function getLastUsersRespond($post_id) {

        $users = $this->redis->lrange(self::TYPE_POST.":responses-of-users:".$post_id, 0, -1);

        $stmt = $this->db->executeQuery("SELECT usr.profile_picture 
        FROM ".self::T_USER." as usr
        WHERE user_id IN (?) LIMIT 3",
            array($users),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();
        return $stmt->fetchAll();

    }

}