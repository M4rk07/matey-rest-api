<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 5:04 PM
 */

namespace App\OAuth2Models;


use App\MateyModels\AbstractModel;
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

    public function getSetFunction (array $props, $type = 'get')
    {
        if ($props['key'] == 'scope') {
            if ($type == 'get') return $this->getScope();
            else return $this->setScope($props['value']);
        }
    }

}