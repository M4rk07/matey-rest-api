<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 12.55
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;
use App\Paths\Paths;
use AuthBucket\OAuth2\Model\ModelInterface;

class Post extends AbstractModel
{

    protected $postId;
    protected $groupId;
    protected $userId;
    protected $title;
    protected $text;
    protected $timeC;
    protected $attachsNum;
    protected $locationsNum;
    protected $archived;
    protected $deleted;

    protected $lastActions;
    protected $numOfReplies;
    protected $numOfShares;
    protected $numOfBoosts;
    protected $score;

    public function setId($id) {
        return $this->setPostId($id);
    }
    public function getId() {
        return $this->getPostId();
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId)
    {
        $this->postId = (int)$postId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = is_null($groupId) ? $groupId : (int)$groupId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeC()
    {
        return $this->timeC;
    }

    /**
     * @param mixed $timeC
     */
    public function setTimeC($timeC)
    {
        $this->timeC = $timeC;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttachsNum()
    {
        return $this->attachsNum;
    }

    /**
     * @param mixed $attachsNum
     */
    public function setAttachsNum($attachsNum)
    {
        $this->attachsNum = (int)$attachsNum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationsNum()
    {
        return $this->locationsNum;
    }

    /**
     * @param mixed $locationsNum
     */
    public function setLocationsNum($locationsNum)
    {
        $this->locationsNum = (int)$locationsNum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * @param mixed $archived
     */
    public function setArchived($archived)
    {
        $this->archived = (int)$archived;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = (int)$deleted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastActions()
    {
        return $this->lastActions;
    }

    /**
     * @param mixed $lastActions
     */
    public function setLastActions($lastActions)
    {
        $this->lastActions = $lastActions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfReplies()
    {
        return $this->numOfReplies;
    }

    /**
     * @param mixed $numOfReplies
     */
    public function setNumOfReplies($numOfReplies)
    {
        $this->numOfReplies = (int)$numOfReplies;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfShares()
    {
        return $this->numOfShares;
    }

    /**
     * @param mixed $numOfShares
     */
    public function setNumOfShares($numOfShares)
    {
        $this->numOfShares = (int)$numOfShares;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfBoosts()
    {
        return $this->numOfBoosts;
    }

    /**
     * @param mixed $numOfBoosts
     */
    public function setNumOfBoosts($numOfBoosts)
    {
        $this->numOfBoosts = (int)$numOfBoosts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        $timeCreated = $this->getTimeC()->getTimestamp();
        $now = new \DateTime(DefaultDates::DATE_FORMAT);
        $now = $now->getTimestamp();

        $numOfBoosts = $this->getNumOfBoosts();
        $numOfReplies = $this->getNumOfReplies();

        if($numOfBoosts == 0) $numOfBoosts = 1;

        return ((1/($now-$timeCreated))*0.5)+($numOfBoosts*0.3)+((1/$numOfReplies)*0.2);
    }

    public function serialize() {
        return serialize(array(
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'text' => $this->text
        ));
    }

    public function unserialize($data) {
        $data = unserialize($data);
        $this->setId($data['post_id'])
            ->setUserId($data['user_id'])
            ->setText($data['text']);
        return $this;
    }

    public function getSetFunction (array $props, $type = 'get')
    {
        if ($props['key'] == 'post_id') {
            if ($type == 'get') return $this->getPostId();
            else return $this->setPostId($props['value']);
        }
        else if ($props['key'] == 'user_id') {
            if ($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if ($props['key'] == 'group_id') {
            if ($type == 'get') return $this->getGroupId();
            else return $this->setGroupId($props['value']);
        }
        else if ($props['key'] == 'title') {
            if ($type == 'get') return $this->getTitle();
            else return $this->setTitle($props['value']);
        }
        else if ($props['key'] == 'text') {
            if ($type == 'get') return $this->getText();
            else return $this->setText($props['value']);
        }
        else if($props['key'] == 'time_c') {
            if($type == 'get') return $this->getTimeC();
            else return $this->setTimeC($this->createDateTimeFromString($props['value']));
        }
        else if ($props['key'] == 'attachs_num') {
            if ($type == 'get') return $this->getAttachsNum();
            else return $this->setAttachsNum($props['value']);
        }
        else if ($props['key'] == 'locations_num') {
            if ($type == 'get') return $this->getLocationsNum();
            else return $this->setLocationsNum($props['value']);
        }
        else if ($props['key'] == 'num_of_shares') {
            if ($type == 'get') return $this->getNumOfShares();
            else return $this->setNumOfShares($props['value']);
        }
        else if ($props['key'] == 'num_of_replies') {
            if ($type == 'get') return $this->getNumOfReplies();
            else return $this->setNumOfReplies($props['value']);
        }
        else if ($props['key'] == 'num_of_boosts') {
            if ($type == 'get') return $this->getNumOfBoosts();
            else return $this->setNumOfBoosts($props['value']);
        }
        else if ($props['key'] == 'score') {
            if ($type == 'get') return $this->getScore();
        }
        else if ($props['key'] == 'archived') {
            if ($type == 'get') return $this->getArchived();
            else return $this->setArchived($props['value']);
        }
        else if ($props['key'] == 'deleted') {
            if ($type == 'get') return $this->getDeleted();
            else return $this->setDeleted($props['value']);
        }
    }


}