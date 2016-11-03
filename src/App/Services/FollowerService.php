<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 14.13
 */

namespace App\Services;


use App\Algos\ActivityWeights;
use App\Controllers\AbstractController;
use App\MateyManagers\UserManager;
use App\MateyModels\User;

class FollowerService extends ActivityService
{

    public function createFollow(User $fromUser, User $toUser) {

        $this->db->insert(self::T_FOLLOWER, array(
            'from_user' => $fromUser->getUserId(),
            'to_user' => $toUser->getUserId()
        ));
        $userManager = new UserManager();
        $userManager->incrUserNumOfFollowing($fromUser, 1);
        $userManager->incrUserNumOfFollowers($toUser, 1);
        $this->incrUserRelationship($fromUser, $toUser, ActivityWeights::FOLLOW_SCORE, AbstractController::returnTime());
        $this->createNewConnection($fromUser, $toUser);

    }

    public function incrUserRelationship (User $fromUser, User $toUser, $score, $now_time) {
        $this->redis->hincrby(self::KEY_POST.":".self::SUBKEY_RELATIONSHIP.":".$fromUser->getUserId().":".$toUser->getUserId(),
            self::FIELD_SCORE, $score);
        $this->redis->hset(self::KEY_POST.":".self::SUBKEY_RELATIONSHIP.":".$fromUser->getUserId().":".$toUser->getUserId(),
            self::FIELD_TIME, $now_time);
    }

    public function createNewConnection (User $user, User $connectedUser) {
        $this->redis->sadd(self::KEY_USER.":".self::SUBKEY_CONNECTIONS.":".$user->getUserId(), $connectedUser->getUserId());
    }

    public function deleteConnection(User $user, User $connectedUser) {
        $this->redis->srem(self::KEY_USER.":".self::SUBKEY_CONNECTIONS.":".$user->getUserId(), $connectedUser->getUserId());
    }

    public function deleteFollow(User $fromUser, User $toUser) {

        $this->db->delete(self::T_FOLLOWER, array(
            'from_user' => $fromUser->getUserId(),
            'to_user' => $toUser->getUserId()
        ));

        $userManager = new UserManager();
        $userManager->incrUserNumOfFollowing($fromUser, -1);
        $userManager->incrUserNumOfFollowers($toUser, -1);
        $this->deleteConnection($fromUser, $toUser);

    }

    public function returnFollowers ($ofUser) {

        return $this->db->fetchAll("SELECT flw.from_user FROM ".self::T_FOLLOWER." as flw WHERE flw.to_user = ?",
            array($ofUser));

    }

}