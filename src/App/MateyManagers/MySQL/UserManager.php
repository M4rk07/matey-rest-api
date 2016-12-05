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

    const SUBKEY_USER_ID = "user-id";

    const FIELD_NUM_OF_FOLLOWING = "num_of_following";
    const FIELD_NUM_OF_FOLLOWERS = "num_of_followers";
    const FIELD_NUM_OF_POSTS = "num_of_posts";
    const FIELD_NUM_OF_GIVEN_RESPONSES = "num_of_given_responses";
    const FIELD_NUM_OF_RECEIVED_RESPONSES = "num_of_received_responses";
    const FIELD_NUM_OF_GIVEN_APPROVES = "num_of_given_approves";
    const FIELD_NUM_OF_RECEIVED_APPROVES = "num_of_received_approves";
    const FIELD_NUM_OF_BEST_RESPONSES = "num_of_best_responses";
    const FIELD_NUM_OF_SHARES = "num_of_shares";

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

    public function getKeyName()
    {
        return "USER";
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

    public function getFollowers($id, $limit, $offset) {
        $all = $this->db->fetchAll("SELECT m_user.user_id, m_user.first_name, m_user.last_name, m_user.full_name, m_user.location, m_user.state 
        FROM ".self::T_FOLLOWER." as m_follower 
        JOIN ".self::T_USER." as m_user ON (m_follower.from_user = m_user.user_id) 
        WHERE m_follower.to_user = ? LIMIT ".$limit." OFFSET ".$offset,
            array($id));

        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function getFollowing($id, $limit, $offset) {
        $all = $this->db->fetchAll("SELECT m_user.user_id, m_user.first_name, m_user.last_name, m_user.full_name, m_user.location, m_user.state 
        FROM ".self::T_FOLLOWER." as m_follower 
        JOIN ".self::T_USER." as m_user ON (m_follower.to_user = m_user.user_id) 
        WHERE m_follower.from_user = ? LIMIT ".$limit." OFFSET ".$offset,
            array($id));

        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function initializeUserStatistics(User $user) {
        $this->redis->hmset(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_FOLLOWING => 0,
            self::FIELD_NUM_OF_POSTS => 0,
            self::FIELD_NUM_OF_GIVEN_APPROVES => 0,
            self::FIELD_NUM_OF_RECEIVED_APPROVES => 0,
            self::FIELD_NUM_OF_GIVEN_RESPONSES => 0,
            self::FIELD_NUM_OF_RECEIVED_RESPONSES => 0,
            self::FIELD_NUM_OF_BEST_RESPONSES => 0,
            self::FIELD_NUM_OF_SHARES => 0
        ));
    }

    public function getUserStatistics (User $user) {

        $userStatistics = $this->redis->hgetall(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId());

        $user->setValuesFromArray($userStatistics);

        return $user;

    }

    public function incrNumOfFollowers(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_FOLLOWERS, $incrBy);
    }

    public function decrNumOfFollowers(User $user, $decrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_FOLLOWERS, $decrBy);
    }

    public function incrNumOfFollowing(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_FOLLOWING, $incrBy);
    }

    public function decrNumOfFollowing(User $user, $decrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_FOLLOWING, $decrBy);
    }

    public function incrNumOfPosts(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_POSTS, $incrBy);
    }

    public function incrNumOfGivenApproves(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_GIVEN_APPROVES, $incrBy);
    }

    public function incrNumOfReceivedApproves(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_RECEIVED_APPROVES, $incrBy);
    }

    public function incrNumOfGivenResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_GIVEN_RESPONSES, $incrBy);
    }

    public function incrNumOfReceivedResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_RECEIVED_RESPONSES, $incrBy);
    }

    public function incrNumOfBestResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_BEST_RESPONSES, $incrBy);
    }

    public function incrNumOfShares(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":".self::SUBKEY_STATISTICS.":".$user->getId(), self::FIELD_NUM_OF_SHARES, $incrBy);
    }

    public function initializeUserIdByEmail (User $user) {
        $this->redis->set(self::KEY_USER.":".self::SUBKEY_USER_ID.":".$user->getEmail(), $user->getId());
    }

    public function getUserIdByEmail ($email) {
        return $this->redis->get(self::KEY_USER.":".self::SUBKEY_USER_ID.":".$email);
    }



}