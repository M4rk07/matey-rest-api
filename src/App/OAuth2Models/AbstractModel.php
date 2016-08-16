<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 11:48 AM
 */

namespace App\OAuth2Models;

use AuthBucket\OAuth2\Model\ModelInterface;

abstract class AbstractModel implements ModelInterface
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    // sets protected class variables from array values
    public abstract function setValuesFromArray ($values);

    // returns key => value array from protected class variables
    public abstract function getValuesAsArray (ModelInterface $model);

}