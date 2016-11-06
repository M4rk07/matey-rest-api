<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 12.56
 */

namespace App\MateyModels;


use App\MateyModels\Post;
use App\MateyModels\User;
use App\Services\BaseService;

class PostManager extends AbstractManager
{

    public function __construct () {
        parent::__construct(self::T_POST, 'App\\MateyModels\\Post');
    }

    public function createPost(Post $post) {

        $result = $this->db->executeUpdate("INSERT INTO ".self::T_POST." (user_id, text, date_time) VALUES (?,?,?)",
            array($post->getUserId(), $post->getText(), $post->getDateTime()));

        if($result<=0) return false;
        $post->setPostId($this->db->lastInsertId());
        $this->initializePostStatistics($post);
        return $post;
    }

    public function initializePostStatistics(Post $post) {
        $this->redis->hmset(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post->getPostId(), array(
            self::FIELD_NUM_OF_GIVEN_RESPONSES => 0,
            self::FIELD_NUM_OF_SHARES => 0
        ));
    }

    public function deletePostStatistics(Post $post) {
        $this->redis->hdel(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post->getPostId(), array(
            self::FIELD_NUM_OF_GIVEN_RESPONSES,
            self::FIELD_NUM_OF_SHARES
        ));
    }

    public function deletePost(Post $post) {

        $result = $this->db->executeUpdate("UPDATE ".self::T_POST." SET deleted = 1 WHERE post_id = ?",
            array($post->getPostId()));

        if($result<=0) return false;
        $this->deletePostStatistics($post);
        return $post;

    }

    public function pushLastResponseToPost (Post $post, User $user) {
        $this->redis->lpush(self::KEY_POST.":".self::SUBKEY_LAST_RESPONSES.":".$post->getPostId(), $user->getUserId());
        $this->redis->ltrim(self::KEY_POST.":".self::SUBKEY_LAST_RESPONSES.":".$post->getPostId(), 0, 3);
    }

    public function incrPostNumOfResponses(Post $post, $incrby) {
        $this->redis->hincrby(self::KEY_POST.":".self::SUBKEY_STATISTICS.":".$post->getPostId(), self::FIELD_NUM_OF_GIVEN_RESPONSES, $incrby);
    }

}