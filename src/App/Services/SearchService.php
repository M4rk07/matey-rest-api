<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.3.17.
 * Time: 16.54
 */

namespace App\Services;


use App\MateyModels\Group;
use App\MateyModels\Post;
use App\MateyModels\User;
use App\Paths\Paths;
use AuthBucket\OAuth2\Model\ModelInterface;
use Foolz\SphinxQL\Drivers\Mysqli\Connection;
use Foolz\SphinxQL\SphinxQL;
use NilPortugues\Sphinx\SphinxClient;
use Predis\Client;

define("IND_MATEY_USER", 0);
define("IND_MATEY_GROUP", 1);
define("IND_MATEY_POST", 3);

class SearchService
{
    protected $clientQL;
    protected $clientAPI;
    protected $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
        $this->clientQL = new Connection();
        $this->clientAPI = new SphinxClient();
        $this->clientQL->setParams(array('host' => Paths::BASE_IP, 'port' => Paths::SPHINXQL_PORT));
        $this->clientAPI->setServer(Paths::BASE_IP, Paths::SPHINX_PORT);
    }

    public function getAutocomplete ($query) {
        $this->clientAPI->setMatchMode(SPH_MATCH_EXTENDED);
        $this->clientAPI->setRankingMode(SPH_RANK_PROXIMITY_BM25);
        $this->clientAPI->setLimits(0, 10);
        $this->clientAPI->setGroupBy ( "auto_text", SPH_GROUPBY_ATTR, "@count desc");
        $result = $this->clientAPI->query($query, 'autocomplete_rt');

        $finalResult['data'] = array();
        if(isset($result['matches'])) {
            foreach ($result['matches'] as $res) {
                $finalResult['data'][] = $res['attrs']['auto_text'];
            }
        }

        return $finalResult;
    }

    public function addToAutocomplete ($text) {
        $this->redis->incr('AUTOCOMPLETE:id');
        $id = $this->redis->get('AUTOCOMPLETE:id');

        $query = SphinxQL::create($this->clientQL)->insert()->into('autocomplete_rt');
        $query->set(array(
            'id' => $id,
            'text' => $text,
            'auto_text' => $text
        ));
        $query->execute();
    }

    public function search ($query, $limit, $offset, $type) {
        $this->clientAPI->setMatchMode(SPH_MATCH_EXTENDED);
        $this->clientAPI->setRankingMode(SPH_RANK_SPH04);
        $this->clientAPI->setLimits($offset, $limit);

        if($type == IND_MATEY_USER) {
            return $this->clientAPI->query($query, 'matey_user_rt');
        } else if($type == IND_MATEY_GROUP) {
            return $this->clientAPI->query($query, 'matey_group_rt');
        } else if($type == IND_MATEY_POST) {
            return $this->clientAPI->query($query, 'matey_post_rt');
        }
    }

    public function addUserToSearch (User $user) {
        $query = SphinxQL::create($this->clientQL)->insert()->into('matey_user_rt');
        $query->set(array(
            'id' => $user->getUserId(),
            'user_id' => $user->getUserId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName()
        ));
        $query->execute();
        $fullName = $user->getFirstName() . " " . $user->getLastName();
        $this->addToAutocomplete($fullName);
    }

    public function addGroupToSearch (Group $group) {
        $query = SphinxQL::create($this->clientQL)->insert()->into('matey_group_rt');
        $query->set(array(
            'id' => $group->getGroupId(),
            'group_id' => $group->getGroupId(),
            'group_name' => $group->getGroupName()
        ));
        $query->execute();
        $this->addToAutocomplete($group->getGroupName());
    }

    public function addPostToSearch (Post $post) {
        $query = SphinxQL::create($this->clientQL)->insert()->into('matey_post_rt');
        $query->set(array(
            'id' => $post->getPostId(),
            'post_id' => $post->getPostId(),
            'title' => $post->getTitle()
        ));
        $query->execute();
        $this->addToAutocomplete($post->getTitle());
    }

}