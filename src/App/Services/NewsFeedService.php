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

    public function getPostIds($user_id, $start, $count) {

        return $this->redis->lrange("newsfeed:posts:".$user_id, $start, $start+$count);

    }

    public function getPosts ($posts, $limit) {

        $this->db->executeQuery("SELECT * 
        FROM ".self::T_ACTIVITY." 
        WHERE source_id IN (?) AND parent_type = POST LIMIT ".$limit,
            array($posts),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

    }

    public function getStatistics($post_id) {

        return $this->redis->hgetall("post:statistics:".$post_id);

    }

    public function getLastUsersRespond($post_id) {

        $users = $this->redis->lrange("post:responses:user:".$post_id, 0, -1);

        return $this->db->executeQuery("SELECT * 
        FROM ".self::T_USER." 
        WHERE user_id IN (?) LIMIT 3",
            array($users),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

    }

}