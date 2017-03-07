<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 20.03
 */

namespace App\MateyModels;


class Location
{

    protected $parentId;
    protected $parentType;
    protected $latt;
    protected $longt;

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentType()
    {
        return $this->parentType;
    }

    /**
     * @param mixed $parentType
     */
    public function setParentType($parentType)
    {
        $this->parentType = $parentType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLatt()
    {
        return $this->latt;
    }

    /**
     * @param mixed $latt
     */
    public function setLatt($latt)
    {
        $this->latt = $latt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLongt()
    {
        return $this->longt;
    }

    /**
     * @param mixed $longt
     */
    public function setLongt($longt)
    {
        $this->longt = $longt;
        return $this;
    }

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'parent_id') {
            if($type == 'get') return $this->getParentId();
            else return $this->setParentId($props['value']);
        }
        else if($props['key'] == 'parent_type') {
            if($type == 'get') return $this->getParentType();
            else return $this->setParentType($props['value']);
        }
        else if($props['key'] == 'latt') {
            if($type == 'get') return $this->getLatt();
            else return $this->setLatt($props['value']);
        }
        else if($props['key'] == 'longt') {
            if($type == 'get') return $this->getLongt();
            else return $this->setLongt($props['value']);
        }
    }

}