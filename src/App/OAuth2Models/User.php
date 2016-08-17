<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 6:45 PM
 */

namespace App\OAuth2Models;


use AuthBucket\OAuth2\Model\ModelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends AbstractModel implements ModelInterface, UserInterface
{

    protected $username;
    protected $password;
    protected $roles;

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }



    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function setValuesFromArray($values)
    {
        $this->id = $values['user_id'];

        $this->username = $values['email'];
        $this->password = $values['password'];
        $this->roles = $this->createScopeArrayFromString($values['roles']);

    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array (
            'email' => $model->getUsername(),
            'password' => $model->getPassword(),
            'roles' => $this->createScopeStringFromArray($model->getRoles())
        );

        return $keyValues;
    }

}