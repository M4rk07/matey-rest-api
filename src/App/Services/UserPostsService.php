<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 9/3/2016
 * Time: 5:09 PM
 */

namespace App\Services;


class UserPostsService extends BaseService
{

    function fetchNewsFeedPosts ($id_user_requesting) {

        return $this->db->fetchAll("SELECT DISTINCT usr1.user_id as user_posted_id, usr1.first_name as user_posted_first_name, usr1.last_name as user_posted_last_name,
        usr_posts.id_post as post_id, usr_posts.text as post_text, usr_posts.date_created as post_date, usr_interests.id_interest as interest_id, usr_interests.name as post_interest,
        usr2.first_name as user_replied_first_name, usr2.last_name as user_replied_last_name,
        usr_post_rply.id_reply as reply_id, usr_post_rply.id_user as reply_user_id, usr_post_rply.text as reply_text, usr_post_rply.date_created as reply_date,
        usr_rply_aprv.id_user as aprv_user_id
        FROM " . $this->USERS_FRIENDS_TABLE . " as usr_friends
        INNER JOIN " . $this->USER_POSTS_TABLE . " as usr_posts ON (usr_posts.id_user = usr_friends.id_user_one OR usr_posts.id_user = usr_friends.id_user_two)
        INNER JOIN " . $this->USERS_TABLE . " as usr1 ON (usr1.user_id = usr_posts.id_user)
        LEFT JOIN " . $this->USERS_POST_INTEREST_TABLE . " as usr_post_interests ON (usr_post_interests.id_post = usr_posts.id_post)
        LEFT JOIN " . $this->USER_INTERESTS_TABLE . " as usr_interests ON (usr_interests.id_interest = usr_post_interests.id_interest)
        LEFT JOIN " . $this->USER_POST_REPLIES_TABLE . " as usr_post_rply ON (usr_post_rply.id_post = usr_posts.id_post)
        LEFT JOIN " . $this->USERS_TABLE . " as usr2 ON (usr_post_rply.id_user = usr2.user_id)
        LEFT JOIN " . $this->USER_REPLY_APPROVES_TABLE . " as usr_rply_aprv ON (usr_rply_aprv.id_reply = usr_post_rply.id_reply)
        WHERE usr_friends.id_user_one = ? OR usr_friends.id_user_two = ? ORDER BY post_date DESC",
            array($id_user_requesting, $id_user_requesting));

    }

}