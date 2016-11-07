<?php

namespace App\MateyModels;
use App\Algos\Algo;
use App\MateyModels\Activity;
use App\MateyModels\User;
use App\Security\SaltGenerator;
use App\Services\BaseService;
use App\Services\CloudStorageService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 00.14
 */
class UserManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\User';
    }

    public function getTableName() {
        return self::T_USER;
    }

    public function loadUserByEmail($email)
    {
        $result = $this->db->fetchAll("SELECT *
        FROM ".self::T_USER."
        WHERE email = ? LIMIT 1",
            array($email));

        $models = $this->makeObjects($result);

        return is_array($models) ? reset($models) : $models;
    }

    public function supportsClass($class)
    {
        return get_class($this) === $class
        || is_subclass_of($class, get_class($this));
    }
    // **********************************************************************************************************
    // **********************************************************************************************************
    // **********************************************************************************************************




    public function loadUserDataById($user_id) {

        $result = $this->db->fetchAll("SELECT m_user.*, f_user.fb_id 
          FROM ".self::T_USER." as m_user
         LEFT JOIN ".self::T_FACEBOOK_INFO." as f_user USING(user_id) 
         WHERE m_user.user_id = ? LIMIT 1",
            array($user_id));

        $result = $result[0];
        $user = new User();
        $user->setUserId($user_id)
            ->setFirstName($result['first_name'])
            ->setLastName($result['last_name'])
            ->setFullName($result['full_name'])
            ->setUsername($result['email'])
            ->setFirstLogin($result['first_login'])
            ->setSilhouette($result['is_silhouette'])
            ->setFbId($result['fb_id']);

        return $user;

    }

    public function setFullName(User $user) {
        $user->setFullName($user->getFirstName()." ".$user->getLastName());
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

    public function setUserFirstTimeLogged (User $user) {

        return $this->db->executeUpdate("UPDATE ".self::T_USER." SET first_login = 1 WHERE user_id = ?",
            array($user->getUserId()));

    }

    public function getSuggestedFollowingsByFacebook(User $user, $fbIds, $pictureSize = 'small') {
        $stmt = $this->db->executeQuery("SELECT m_usr.user_id, m_usr.first_name, m_usr.last_name FROM ".self::T_FACEBOOK_INFO." as m_f_info
        INNER JOIN ".self::T_USER." as m_usr USING(user_id)
        WHERE m_f_info.fb_id IN(?)",
            array($fbIds),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $stmt->execute();
        $result = $stmt->fetchAll();

        $suggestedFriends = array();
        $cloudStorage = new CloudStorageService();


        foreach($result as $res) {
            $suggestedFriends[] = array(
                'user_id' => $res['user_id'],
                'first_name' => $res['first_name'],
                'last_name' => $res['last_name'],
                'profile_picture' => $cloudStorage->generateProfilePictureLink($res['user_id'], $pictureSize)
            );
        }

        return $suggestedFriends;

    }



}