<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.22
 */

namespace App\Services;


class PostService extends BaseService
{

    public function createPost($user_id, $text) {

        $this->db->insert(self::T_POST, array(
            'user_id' => $user_id,
            'text' => $text
        ));
        return $this->db->lastInsertId();

    }

    public function createActivity($user_id, $source_id, $parent_type, $activity_type, $srl_data) {

        $this->db->insert(self::T_ACTIVITY, array(
            'user_id' => $user_id,
            'source_id' => $source_id,
            'parent_type' => $parent_type,
            'activity_type' => $activity_type,
            'srl_data' => $srl_data
        ));

    }

}