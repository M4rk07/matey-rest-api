<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 5:04 PM
 */

namespace App\OAuth2Models;


use AuthBucket\OAuth2\Model\ModelInterface;
use AuthBucket\OAuth2\Model\ScopeInterface;

class Scope extends AbstractModel implements ScopeInterface
{
    protected $scope;

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param mixed $scope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }



    public function setValuesFromArray($values)
    {
        $this->id = $values['id'];

        $this->scope = $values['scope'];
    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array (
            'id' => $model->getId(),
            'scope' => $model->getScope()
        );

        return $keyValues;
    }

}