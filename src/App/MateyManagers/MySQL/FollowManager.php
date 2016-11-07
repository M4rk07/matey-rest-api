<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 15.00
 */

namespace App\MateyModels;


use App\Algos\ActivityWeights;
use App\Algos\Timer;
use App\MateyModels\Follow;
use App\MateyModels\User;
use App\Services\BaseService;

class FollowManager extends AbstractManager
{

    public function __construct ($db) {
        parent::__construct($db);
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\Follow';
    }

    public function getTableName() {
        return self::T_FOLLOWER;
    }

    public function createFollow(Follow $follow) {

        $this->db->insert(self::T_FOLLOWER, array(
            'from_user' => $follow->getUserFrom(),
            'to_user' => $follow->getUserTo(),
            'date_started' => $follow->getDateTime()
        ));

        $this->incrUserRelationship($follow, ActivityWeights::FOLLOW_SCORE);
        $this->createNewConnection($follow);

    }

    public function incrUserRelationship (Follow $follow, $score) {
        $this->redis->hincrby(self::KEY_POST.":".self::SUBKEY_RELATIONSHIP.":".$follow->getUserFrom().":".$follow->getUserTo(),
            self::FIELD_SCORE, $score);
        $this->redis->hset(self::KEY_POST.":".self::SUBKEY_RELATIONSHIP.":".$follow->getUserFrom().":".$follow->getUserTo(),
            self::FIELD_TIME, strtotime(Timer::returnTime()));
    }

    public function createNewConnection (Follow $follow) {
        $this->redis->sadd(self::KEY_USER.":".self::SUBKEY_CONNECTIONS.":".$follow->getUserFrom(), $follow->getUserTo());
    }

    public function deleteConnection(Follow $follow) {
        $this->redis->srem(self::KEY_USER.":".self::SUBKEY_CONNECTIONS.":".$follow->getUserFrom(), $follow->getUserTo());
    }

    public function deleteFollow(Follow $follow) {

        $this->db->delete(self::T_FOLLOWER, array(
            'from_user' => $follow->getUserFrom(),
            'to_user' => $follow->getUserTo()
        ));

        $this->deleteConnection($follow);

    }

}