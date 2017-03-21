<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 22.18
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultNumbers;
use App\Paths\Paths;
use AuthBucket\OAuth2\Model\ModelInterface;
use Foolz\SphinxQL\Drivers\Mysqli\Connection;
use Foolz\SphinxQL\SphinxQL;

class GroupManager extends AbstractManager
{

    const FIELD_NUM_OF_FOLLOWERS = "num_of_followers";
    const FIELD_NUM_OF_SHARES = "num_of_shares";
    const FIELD_NUM_OF_FAVORITES = "num_of_favorites";

    public function createModel(ModelInterface $model)
    {
        $model = parent::createModel($model);

        $this->initializeGroupStatistics($model);

        $this->addToSearch($model);

        return $model;
    }

    public function addToSearch (Group $group) {
        $client = new Connection();
        $client->setParams(array('host' => Paths::BASE_IP, 'port' => Paths::SPHINXQL_PORT));
        $query = SphinxQL::create($client)->insert()->into($this->getSphinxIndex());
        $query->set(array(
            'id' => $group->getGroupId(),
            'group_id' => $group->getGroupId(),
            'group_name' => $group->getGroupName()
        ));
        $query->execute();
    }

    public function getSearchResults ($ids) {
        $qMarks = "";
        foreach ($ids as $id) {
            $qMarks .= "?,";
        }
        $qMarks = trim($qMarks, ",");

        $all = $this->db->fetchAll("SELECT group_id, group_name FROM ". $this->getTableName().
            " WHERE group_id IN (".$qMarks.") ORDER BY FIELD(group_id, ".$qMarks.")",
            array_merge($ids, $ids));

        $models = $this->makeObjects($all);

        foreach($models as $key => $model) {
            $models[$key] = $this->getGroupStatistics($model);
        }

        return $models;
    }

    public function initializeGroupStatistics(Group $group) {
        $this->redis->hmset($this->getRedisKey().":statistics:".$group->getGroupId(), array(
            self::FIELD_NUM_OF_FOLLOWERS => 0,
            self::FIELD_NUM_OF_SHARES => 0,
            self::FIELD_NUM_OF_FAVORITES => 0
        ));
    }

    public function getGroupStatistics (Group $group) {

        $groupStatistics = $this->redis->hgetall($this->getRedisKey().":statistics:".$group->getGroupId());

        $group->setValuesFromArray($groupStatistics);

        return $group;

    }

    public function readModelBy(array $criteria, array $orderBy = null,
                                $limit = null, $offset = null, array $fields = null)
    {
        if(!isset($criteria['deleted'])) $criteria['deleted'] = 0;

        $models = parent::readModelBy($criteria, $orderBy, $limit, $offset, $fields);

        if($fields === null || count(array_intersect($this->getRedisFields(), $fields)) && !empty($models)) {

            foreach($models as $key => $model) {
                $models[$key] = $this->getGroupStatistics($model);
            }

        }

        return $models;
    }

    public function incrNumOfFollowers(Group $group, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$group->getGroupId(), self::FIELD_NUM_OF_FOLLOWERS, $incrBy);
    }

    public function incrNumOfShares(Group $group, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$group->getGroupId(), self::FIELD_NUM_OF_SHARES, $incrBy);
    }

    public function incrNumOfFavorites(Group $group, $incrBy = 1) {
        $this->redis->hincrby($this->getRedisKey().":statistics:".$group->getGroupId(), self::FIELD_NUM_OF_FAVORITES, $incrBy);
    }

    public function pushDeck (Group $group, $posts) {
        if(!is_array($posts)) $posts = array($posts);
        foreach($posts as $post)
            $this->redis->lpush($this->getRedisKey().":deck:".$group->getGroupId(), $post->getPostId());

        $this->redis->ltrim($this->getRedisKey().":deck:".$group->getGroupId(), 0, DefaultNumbers::DECK_CAPACITY);
    }

    public function getDeck (Group $group, $start = 0, $stop = -1) {
        return $this->redis->lrange($this->getRedisKey().":deck:".$group->getGroupId(), $start, $stop);
    }

}