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
use AuthBucket\OAuth2\Model\ModelInterface;
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

    public function createModel(ModelInterface $model, $ignore = false)
    {
        $model = parent::createModel($model, $ignore);

        $this->initializeUserStatistics($model);

        return $model;
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, array $fields = null) {

        $models = parent::readModelBy($criteria, $orderBy, $limit, $offset, $fields);

        if($fields == null || (in_array('counts', $fields) && !empty($models))) {

            foreach($models as $key => $model) {
                $models[$key] = $this->getUserStatistics($model);
            }

        }

        return $models;

    }

    public function initializeUserStatistics(User $user) {
        $this->redis->hmset(self::KEY_USER.":counts:".$user->getId(), array(
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

        $userStatistics = $this->redis->hgetall(self::KEY_USER.":counts:".$user->getId());

        $user->setValuesFromArray($userStatistics);

        return $user;

    }

    public function incrNumOfFollowers(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_FOLLOWERS, $incrBy);
    }

    public function decrNumOfFollowers(User $user, $decrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_FOLLOWERS, $decrBy);
    }

    public function incrNumOfFollowing(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_FOLLOWING, $incrBy);
    }

    public function decrNumOfFollowing(User $user, $decrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_FOLLOWING, $decrBy);
    }

    public function incrNumOfPosts(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_POSTS, $incrBy);
    }

    public function incrNumOfGivenApproves(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_GIVEN_APPROVES, $incrBy);
    }

    public function incrNumOfReceivedApproves(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_RECEIVED_APPROVES, $incrBy);
    }

    public function incrNumOfGivenResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_GIVEN_RESPONSES, $incrBy);
    }

    public function incrNumOfReceivedResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_RECEIVED_RESPONSES, $incrBy);
    }

    public function incrNumOfBestResponses(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_BEST_RESPONSES, $incrBy);
    }

    public function incrNumOfShares(User $user, $incrBy = 1) {
        $this->redis->hincrby(self::KEY_USER.":counts:".$user->getId(), self::FIELD_NUM_OF_SHARES, $incrBy);
    }

    public function pushFeed (User $user, $feeds) {
        $this->redis->zadd($this->getKeyName().":feed_scored:".$user->getId(), $feeds);
    }

    public function getFeed (User $user, $start = 0, $stop = 10) {
        return $this->redis->zrange($this->getKeyName().":feed_scored:".$user->getId(), $start, $stop);
    }

    public function pushFeedSeen (User $user, $ids) {
        $this->redis->sadd($this->getKeyName().":feed_seen:".$user->getId(), $ids);
    }

    public function getFeedSeen (User $user) {
        return $this->redis->smembers($this->getKeyName().":feed_seen:".$user->getId());
    }

    public function isFeedSeen (User $user, $id) {
        $seen = $this->redis->sismember($this->getKeyName().":feed_seen:".$user->getId(), $id);
        if(empty($seen)) return false;
        return true;
    }

}