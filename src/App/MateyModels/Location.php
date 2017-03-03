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

    public function setValuesFromArray($values)
    {
        $this->parentId = isset($values['parent_id']) ? $values['parent_id'] : "";
        $this->parentType= isset($values['parent_type']) ? $values['parent_type'] : "";
        $this->latt = isset($values['latt']) ? $values['longt'] : "";
        $this->longt = isset($values['longt']) ? $values['longt'] : "";
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->parentId) ? : $keyValues['parent_id'] = $this->parentId;
        empty($this->parentType) ? : $keyValues['parent_type'] = $this->parentType;
        empty($this->latt) ? : $keyValues['latt'] = $this->latt;
        empty($this->longt) ? : $keyValues['longt'] = $this->longt;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        return $keyValues;
    }

}