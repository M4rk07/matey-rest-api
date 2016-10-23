<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.10.16.
 * Time: 14.37
 */

namespace App\Services;


class ActivityService extends BaseService
{

    public function createActivity($user_id, $source_id, $activity_type, $parent_id, $parent_type, $srl_data) {

        $this->db->insert(self::T_ACTIVITY, array(
            'user_id' => $user_id,
            'source_id' => $source_id,
            'activity_type' => $activity_type,
            'parent_id' => $parent_id,
            'parent_type' => $parent_type,
            'srl_data' => $srl_data
        ));

        return $this->db->lastInsertId();

    }

    public function deleteActivity($post_id, $parent_type) {

        $this->db->delete(self::T_ACTIVITY, array(
            'source_id' => $post_id,
            'parent_type' => $parent_type
        ));

    }

    public function getActivityOne ($activity_id) {
        $activity = $this->db->fetchAll('SELECT * FROM '.self::T_ACTIVITY.' WHERE activity_id = ? LIMIT 1',
            array($activity_id));

        return $activity[0];
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

}