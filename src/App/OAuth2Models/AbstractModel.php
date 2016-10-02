<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 11:48 AM
 */

namespace App\OAuth2Models;

use AuthBucket\OAuth2\Model\ModelInterface;

abstract class AbstractModel
{
    protected $id;
    protected $dateFormat = 'Y-m-d H:i:s';

    public function getId(){

        return $this->id;

    }

    public function setId($id){

        $this->id = $id;
        return $this;

    }

    public function createDateTimeFromString ($dateTimeString) {

        return \DateTime::createFromFormat($this->dateFormat, $dateTimeString);

    }

    public function createArrayFromString ($string) {

        return explode(" ", $string);

    }

    public function createStringFromArray ($array) {

        return implode(" ", $array);

    }

    // sets protected class variables from array values
    public abstract function setValuesFromArray ($values);

    // returns key => value array from protected class variables
    public abstract function getValuesAsArray (ModelInterface $model);

}