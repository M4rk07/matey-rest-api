<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.22
 */

namespace App\Services;


class PostService extends ActivityService
{

    public function createPost($interest_id, $user_id, $text) {

        $this->db->insert(self::T_POST, array(
            'user_id' => $user_id,
            'text' => $text
        ));

        return $this->db->lastInsertId();

    }

    public function deletePost($post_id, $user_id) {

        $this->db->delete(self::T_POST, array(
            'post_id' => $post_id
        ));

    }

    public function bookmarkPost($post_id, $user_id) {

        $this->db->insert(self::T_BOOKMARK, array(
            'post_id' => $post_id,
            'user_id' => $user_id
        ));

    }

    public function createResponse($user_id, $post_id, $text) {

        $this->db->insert(self::T_RESPONSE, array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'text' => $text
        ));

        return $this->db->lastInsertId();

    }

    public function deleteResponse($response_id, $post_id, $user_id) {

        $this->db->delete(self::T_RESPONSE, array(
            'response_id' => $response_id,
            'post_id' => $post_id,
            'user_id' => $user_id
        ));

    }

    public function approve($user_id, $response_id) {

        $this->db->insert(self::T_APPROVE, array(
            'response_id' => $response_id,
            'user_id' => $user_id
        ));

    }

}