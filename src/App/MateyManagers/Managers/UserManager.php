<?php

namespace App\MateyModels;
use App\Algos\Algo;
use App\Constants\Defaults\DefaultNumbers;
use App\MateyModels\Activity;
use App\MateyModels\User;
use App\Paths\Paths;
use App\Security\SaltGenerator;
use App\Services\BaseService;
use App\Services\CloudStorageService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelInterface;
use Foolz\SphinxQL\Drivers\Mysqli\Connection;
use Foolz\SphinxQL\SphinxQL;
use NilPortugues\Sphinx\SphinxClient;
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
    const FIELD_NUM_OF_GIVEN_REPLIES = "num_of_given_replies";
    const FIELD_NUM_OF_RECEIVED_REPLIES = "num_of_received_replies";
    const FIELD_NUM_OF_GIVEN_APPROVES = "num_of_given_approves";
    const FIELD_NUM_OF_RECEIVED_APPROVES = "num_of_received_approves";
    const FIELD_NUM_OF_BEST_REPLIES = "num_of_best_replies";
    const FIELD_NUM_OF_SHARES = "num_of_shares";

    public function loadUserByEmail($email)
    {
        return $this->readModelOneBy(array(
            'email' => $email
        ));
    }

    public function createModel(ModelInterface $model)
    {
        $model = parent::createModel($model);

        $this->initializeUserStatistics($model);

        return $model;
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, array $fields = null) {

        $models = parent::readModelBy($criteria, $orderBy, $limit, $offset, $fields);

        if($fields === null || count(array_intersect($this->getRedisFields(), $fields)) && !empty($models)) {

            foreach($models as $key => $model) {
                $models[$key] = $this->getUserStatistics($model);
            }

        }

        return $models;

    }

    public function getSearchResults ($ids) {
        $qMarks = "";
        foreach ($ids as $id) {
            $qMarks .= "?,";
        }
        $qMarks = trim($qMarks, ",");

        $all = $this->db->fetchAll("SELECT user_id, first_name, last_name FROM ". $this->getTableName().
            " WHERE user_id IN (".$qMarks.") ORDER BY FIELD(user_id, ".$qMarks.")",
            array_merge($ids, $ids));

        return $this->makeObjects($all);
    }

    public function initializeUserStatistics(User $user) {
        $this->redis->hmset($this->getRedisKey().":statistics:".$user->getUserId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_FOLLOWING => 0,
            self::FIELD_NUM_OF_POSTS => 0,
            self::FIELD_NUM_OF_GIVEN_APPROVES => 0,
            self::FIELD_NUM_OF_RECEIVED_APPROVES => 0,
            self::FIELD_NUM_OF_GIVEN_REPLIES => 0,
            self::FIELD_NUM_OF_RECEIVED_REPLIES => 0,
            self::FIELD_NUM_OF_BEST_REPLIES => 0,
            self::FIELD_NUM_OF_SHARES => 0
        ));
    }

    public function getUserStatistics (User $user) {

        $userStatistics = $this->redis->hgetall($this->getRedisKey().":statistics:".$user->getUserId());

        $user->setValuesFromArray($userStatistics);

        return $user;

    }

    public function incrNumOfFollowers(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_FOLLOWERS, $incrBy);
    }

    public function decrNumOfFollowers(User $user, $decrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_FOLLOWERS, $decrBy);
    }

    public function incrNumOfFollowing(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_FOLLOWING, $incrBy);
    }

    public function decrNumOfFollowing(User $user, $decrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_FOLLOWING, $decrBy);
    }

    public function incrNumOfPosts(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_POSTS, $incrBy);
    }

    public function incrNumOfGivenApproves(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_GIVEN_APPROVES, $incrBy);
    }

    public function incrNumOfReceivedApproves(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_RECEIVED_APPROVES, $incrBy);
    }

    public function incrNumOfGivenReplies(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_GIVEN_REPLIES, $incrBy);
    }

    public function incrNumOfReceivedReplies(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_RECEIVED_REPLIES, $incrBy);
    }

    public function incrNumOfBestReplies(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_BEST_REPLIES, $incrBy);
    }

    public function incrNumOfShares(User $user, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$user->getUserId(), self::FIELD_NUM_OF_SHARES, $incrBy);
    }

    public function pushDeck (User $user, $posts) {
        if(!is_array($posts)) $posts = array($posts);
        foreach($posts as $post)
            $this->redis->lpush($this->getRedisKey().":deck:".$user->getUserId(), $post->getPostId());

        $this->redis->ltrim($this->getRedisKey().":deck:".$user->getUserId(), 0, DefaultNumbers::DECK_CAPACITY);
    }

    public function getDeck (User $user, $start = 0, $stop = -1) {
        return $this->redis->lrange($this->getRedisKey().":deck:".$user->getUserId(), $start, $stop);
    }

    public function pushFeedSeen (User $user, $ids) {
        $this->redis->sadd($this->getRedisKey().":feed_seen:".$user->getUserId(), $ids);
    }

    public function isFeedSeen (User $user, $id) {
        $seen = $this->redis->sismember($this->getRedisKey().":feed_seen:".$user->getUserId(), $id);
        if(empty($seen)) return false;
        return true;
    }

    public function pushLoggedDevice (User $user, $deviceId) {
        $this->redis->sadd($this->getRedisKey().":logged_devices:".$user->getUserId(), $deviceId);
    }

    public function remLoggedDevice (User $user, $deviceId) {
        $this->redis->srem($this->getRedisKey().":logged_devices:".$user->getUserId(), $deviceId);
    }

    public function getLoggedDevices($userId) {
        return $this->redis->smembers($this->getRedisKey().":logged_devices:".$userId);
    }

    public function pushNotification ($userId, $activityId) {
        $this->redis->lpush($this->getRedisKey().":notifications:".$userId, $activityId);
        $this->redis->ltrim($this->getRedisKey().":notifications:".$userId, 0, DefaultNumbers::NOTIFICATION_CAPACITY);
    }

    public function getNotifications ($userId, $start = 0, $stop = -1) {
        return $this->redis->lrange($this->getRedisKey().":notifications:".$userId, $start, $stop);
    }

    public function incrNumOfNewNotifications (User $user, $incrBy = 1) {
        return $this->redis->set($this->getRedisKey().":num_of_new_notifications:".$user->getUserId(), $incrBy);
    }

    public function getNumOfNewNotifications (User $user) {
        return $this->redis->get($this->getRedisKey().":num_of_new_notifications:".$user->getUserId());
    }

    public function resetNumOfNewNotifications(User $user) {
        return $this->redis->set($this->getRedisKey().":num_of_new_notifications:".$user->getUserId(), 0);
    }

}