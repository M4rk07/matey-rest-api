<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.10.16.
 * Time: 16.16
 */

namespace App\Services;


use Symfony\Component\HttpFoundation\Request;

class NewsFeedService extends BaseService
{

    public function getActivityIds($user_id, $start, $count) {

        return $this->redis->lrange("newsfeed:".$user_id, $start, $start+$count);

    }

    public function getActivities ($activity_id, $limit) {

        $stmt = $this->db->executeQuery("SELECT act.*, usr.first_name, usr.last_name, usr.profile_picture 
        FROM ".self::T_ACTIVITY." act
        JOIN ".self::T_USER." as usr USING(user_id)
        WHERE act.activity_id IN (?) LIMIT ".$limit,
            array($activity_id),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();
        return $stmt->fetchAll();

    }

    public function getStatistics($type, $id) {
        if($type == self::TYPE_POST) {
            return $this->redis->hgetall("post:statistics:" . $id);
        } else if ($type == self::TYPE_RESPONSE) {
            return $this->redis->hgetall("response:statistics:" . $id);
        }

    }

    public function getLastUsersRespond($post_id) {

        $users = $this->redis->lrange("post:responses:user:".$post_id, 0, -1);

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