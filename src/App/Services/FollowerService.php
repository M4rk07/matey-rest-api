<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.13
 */

namespace App\Services;


class FollowerService extends ActivityService
{

    public function createFollow($fromUser, $toUser) {

        $this->db->insert(self::T_FOLLOWER, array(
            'from_user' => $fromUser,
            'to_user' => $toUser
        ));

    }

    public function deleteFollow($fromUser, $toUser) {

        $this->db->delete(self::T_FOLLOWER, array(
            'from_user' => $fromUser,
            'to_user' => $toUser
        ));

    }

    public function findFriendsByFbId($fbIds) {

        $stmt = $this->db->executeQuery("SELECT m_usr.user_id, m_usr.full_name, m_usr.profile_picture FROM ".self::T_FACEBOOK_INFO." as m_f_info
        INNER JOIN ".self::T_USER." as m_usr USING(user_id)
        WHERE m_f_info.fb_id IN(?)",
            array($fbIds),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();
        return $stmt->fetchAll();

    }

    public function returnFollowers ($ofUser) {

        return $this->db->fetchAll("SELECT flw.from_user FROM ".self::T_FOLLOWER." as flw WHERE flw.to_user = ?",
            array($ofUser));

    }

}