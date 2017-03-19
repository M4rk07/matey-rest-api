<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.52
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;
use App\Handlers\File\GroupPictureHandler;
use App\Paths\Paths;

class Group extends AbstractModel
{

    const DEFAULT_GROUP = NULL;

    protected $groupId;
    protected $userId;
    protected $groupName;
    protected $description;
    protected $timeC;
    protected $silhouette;
    protected $numOfFollowers;
    protected $numOfShares;
    protected $numOfFavorites;
    protected $deleted;

    public function setId($id) {
        return $this->setGroupId($id);
    }

    public function getId() {
        return $this->getGroupId();
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
        $this->groupId = (int)$groupId;
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
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param mixed $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @param mixed $dateCreated
     */
    public function setTimeC($timeC)
    {
        $this->timeC = $timeC;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isSilhouette()
    {
        return $this->silhouette;
    }

    /**
     * @param mixed $silhouette
     */
    public function setSilhouette($silhouette)
    {
        $this->silhouette = (int)$silhouette;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfFollowers()
    {
        return $this->numOfFollowers;
    }

    /**
     * @param mixed $numOfFollowers
     */
    public function setNumOfFollowers($numOfFollowers)
    {
        $this->numOfFollowers = (int)$numOfFollowers;
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
    public function getNumOfFavorites()
    {
        return $this->numOfFavorites;
    }

    /**
     * @param mixed $numOfFavorites
     */
    public function setNumOfFavorites($numOfFavorites)
    {
        $this->numOfFavorites = (int)$numOfFavorites;
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

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'group_id') {
            if($type == 'get') return $this->getGroupId();
            else return $this->setGroupId($props['value']);
        }
        else if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'group_name') {
            if($type == 'get') return $this->getGroupName();
            else return $this->setGroupName($props['value']);
        }
        else if($props['key'] == 'description') {
            if($type == 'get') return $this->getDescription();
            else return $this->setDescription($props['value']);
        }
        else if($props['key'] == 'time_c') {
            if($type == 'get') return $this->getTimeC();
            else return $this->setTimeC($this->createDateTimeFromString($props['value']));
        }
        else if($props['key'] == 'is_silhouette') {
            if($type == 'get') return $this->isSilhouette();
            else return $this->setSilhouette($props['value']);
        }
        else if($props['key'] == 'num_of_followers') {
            if($type == 'get') return $this->getNumOfFollowers();
            else return $this->setNumOfFollowers($props['value']);
        }
        else if($props['key'] == 'num_of_shares') {
            if($type == 'get') return $this->getNumOfShares();
            else return $this->setNumOfShares($props['value']);
        }
        else if($props['key'] == 'num_of_favorites') {
            if($type == 'get') return $this->getNumOfFavorites();
            else return $this->setNumOfFavorites($props['value']);
        }
        else if($props['key'] == 'deleted') {
            if($type == 'get') return $this->getDeleted();
            else return $this->setDeleted($props['value']);
        }
        else if($props['key'] == 'group_picture_url') {
            if($type == 'get') return GroupPictureHandler::getPictureUrl($this);
        }
    }


}