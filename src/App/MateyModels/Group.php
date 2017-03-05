<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.52
 */

namespace App\MateyModels;


use App\Paths\Paths;

class Group extends AbstractModel
{

    const DEFAULT_GROUP = NULL;

    protected $userId;
    protected $groupName;
    protected $description;
    protected $timeC;
    protected $silhouette;
    protected $numOfFollowers;
    protected $deleted;

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
        $this->userId = $userId;
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
    public function getSilhouette()
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
    public function getGroupPicture($size = 'small')
    {
        $dimension = '100x100';
        if($size != 'small' && in_array($size, array('medium', 'large', 'veryLarge', 'original'))) {
            if($size == 'medium') $dimension = '200x200';
            else if($size == 'large') $dimension = '480x480';
            else if($size == 'original') $dimension = 'originals';
        }
        if($this->silhouette == 1) return "https://tctechcrunch2011.files.wordpress.com/2010/10/pirate.jpg";
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/groups/".$dimension."/".$this->getId().".jpg";
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
        $this->numOfFollowers = $numOfFollowers;
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


    public function setValuesFromArray($values)
    {
        if(isset($values['group_id'])) $this->setId($values['group_id']);
        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['group_name'])) $this->setGroupName($values['group_name']);
        if(isset($values['description'])) $this->setDescription($values['description']);
        if(isset($values['date_created'])) $this->setDateCreated($values['date_created']);
        if(isset($values['is_silhouette'])) $this->setSilhouette($values['is_silhouette']);
        if(isset($values['num_of_followers'])) $this->setNumOfFollowers($values['num_of_followers']);
        if(isset($values['deleted'])) $this->setDeleted($values['deleted']);
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['group_id'] = $this->id;
        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->groupName) ? : $keyValues['group_name'] = $this->groupName;
        empty($this->description) ? : $keyValues['description'] = $this->description;
        empty($this->timeC) ? : $keyValues['time_c'] = $this->timeC;
        empty($this->silhouette) && $this->silhouette !== 0 ? : $keyValues['is_silhouette'] = $this->silhouette;
        empty($this->deleted) && $this->deleted !== 0 ? : $keyValues['deleted'] = $this->deleted;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        empty($this->numOfFollowers) ? : $keyValues['num_of_followers'] = $this->numOfFollowers;
        $keyValues['group_picture_url'] = $this->getGroupPicture('original');

        return $keyValues;
    }


}