<?php

namespace App\MateyManagers;
use App\Algos\Algo;
use App\MateyModels\Activity;
use App\MateyModels\User;
use App\Services\BaseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 00.14
 */
class UserManager extends BaseService implements UserProviderInterface
{

    public function createModel(User $user) {

        $this->db->executeUpdate("INSERT INTO ".self::T_USER." (email, first_name, last_name, full_name, is_silhouette) VALUES (?,?,?,?,?)",
            array($user->getUsername(), $user->getFirstName(), $user->getLastName(), $user->getFullName(), $user->isSilhouette()));

        $user->setUserId($this->db->lastInsertId());

        $this->initializeUserStatistics($user);
        $this->initializeUserIdByEmail($user);

        return $user;

    }

    public function initializeUserStatistics(User $user) {
        $this->redis->hmset(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getUserId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_FOLLOWING => 0,
            self::FIELD_NUM_OF_POSTS => 0,
            self::FIELD_NUM_OF_GIVEN_APPROVES => 0,
            self::FIELD_NUM_OF_RECEIVED_APPROVES => 0,
            self::FIELD_NUM_OF_GIVEN_RESPONSES => 0,
            self::FIELD_NUM_OF_RECEIVED_RESPONSES => 0,
            self::FIELD_NUM_OF_BEST_RESPONSES => 0,
            self::FIELD_NUM_OF_PROFILE_CLICKS => 0,
            self::FILED_NUM_OF_SHARES => 0
        ));
    }

    public function initializeUserIdByEmail (User $user) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_USER_ID.":".$user->getUsername(), $user->getUserId());
    }

    public function createUserCredentials (User $user) {

        $this->db->executeUpdate("INSERT INTO ".self::T_A_USER." (user_id, username, password, salt) VALUES (?,?,?,?)",
            array($user->getUserId(), $user->getUsername(), $user->getPassword(), $user->getSalt()));

        return $user;
    }

    public function createFacebookInfo(User $user) {
        $this->db->executeUpdate("INSERT INTO ".self::T_FACEBOOK_INFO." (user_id, fb_id) VALUES (?,?)",
            array($user->getUserId(), $user->getFbId()));

        $this->pushFbAccessToken($user);

        return $user;
    }

    public function pushFbAccessToken(User $user) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$user->getUserId(), $user->getFbToken());
        $this->redis->expire(self::KEY_USER.":".self::SUBKEY_FB_TOKEN.":".$user->getUserId(), 3600);
    }

    public function loadUserByUsername($username)
    {

        $result = $this->db->fetchAll("SELECT m_user.user_id, o_user.username, o_user.password, o_user.salt, m_f_user.fb_id
        FROM ".self::T_USER." as m_user
        LEFT JOIN ".self::T_A_USER." as o_user USING(user_id)
        LEFT JOIN ".self::T_FACEBOOK_INFO." as m_f_user USING(user_id)
        WHERE email = ? LIMIT 1",
            array($username));
        $user = new User();

        if(empty($result)) return $user;
        $result = $result[0];
        $user->setUserId($result['user_id'])
            ->setUsername($result['username'])
            ->setPassword($result['password'])
            ->setSalt($result['salt'])
            ->setFbId($result['fb_id']);

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {

        $result = $this->db->fetchAll("SELECT m_user.user_id, o_user.username, o_user.password, o_user.salt, m_f_user.fb_id
        FROM ".self::T_USER." as m_user
        LEFT JOIN ".self::T_A_USER." as o_user USING(user_id)
        LEFT JOIN ".self::T_FACEBOOK_INFO." as m_f_user USING(user_id)
        WHERE m_user.user_id = ? LIMIT 1",
            array($user->getUserId()));

        $result = $result[0];
        $user = new User();
        $user->setUserId($result['user_id'])
            ->setUsername($result['username'])
            ->setPassword($result['password'])
            ->setSalt($result['salt'])
            ->setFbId($result['fb_id']);

        return $user;
    }

    public function supportsClass($class)
    {
        return get_class($this) === $class
        || is_subclass_of($class, get_class($this));
    }

    public function incrUserNumOfFollowers(User $user, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getUserId(), self::FIELD_NUM_OF_FOLLOWERS, $incrby);
    }

    public function incrUserNumOfFollowing(User $user, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getUserId(), self::FIELD_NUM_OF_FOLLOWING, $incrby);
    }

    public function incrUserNumOfPosts(User $user, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getUserId(), self::FIELD_NUM_OF_POSTS, $incrby);
    }

    public function incrUserNumOfResponses(User $user, $incrby) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getUserId(), self::FIELD_NUM_OF_GIVEN_RESPONSES, $incrby);
    }

    public function getUserActivities (User $user, $limit) {
        $result = $this->db->fetchAll("SELECT activity_id, activity_time FROM ".self::T_ACTIVITY." WHERE user_id = ? ORDER BY activity_id DESC LIMIT ".$limit,
            array($user->getUserId()));

        $activities = array();
        foreach($result as $res) {
            $activity = new Activity();
            $activity->setActivityId($res['activity_id'])
                ->setActivityTime($res['activity_time']);
            array_push($activities, $activity);
        }

        $user->setActivities($activities);

        return $user;
    }

    public function pushActivitiesToUserFeed($activities, User $user) {

        foreach($activities as $activity) {
            $this->pushActivityToFeed($activity, $user);
        }

    }

    public function pushActivityToFeed(Activity $activity, User $user) {
        $algo = new Algo();
        $score = $algo->calculateActivityTimeScore($activity->getActivityTime());
        $this->redis->zadd(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$user->getUserId(), array(
            $activity->getActivityId() => $score
        ));
        $this->redis->zremrangebyrank(self::KEY_USER.":".self::SUBKEY_NEWSFEED.":".$user->getUserId(), 0, -301);
    }

    public function setUserFirstTimeLogged (User $user) {

        return $this->db->executeUpdate("UPDATE ".self::T_USER." SET first_login = 1 WHERE user_id = ?",
            array($user->getUserId()));

    }

}