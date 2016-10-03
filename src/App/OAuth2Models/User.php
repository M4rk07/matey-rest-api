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
    protected $salt;
    protected $first_name;
    protected $last_name;

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
        return $this;
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
        return $this;
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
        return $this;
    }



    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }



    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function setValuesFromArray($values)
    {
        $this->id = $values['id_user'];

        $this->username = $values['email'];
        $this->password = $values['password'];
        $this->salt = $values['salt'];
        $this->roles = $this->createArrayFromString($values['roles']);
        $this->first_name = $values['first_name'];
        $this->last_name = $values['last_name'];

    }

    public function getValuesAsArray(ModelInterface $model)
    {
        $keyValues = array (
            'email' => $model->getUsername(),
            'password' => $model->getPassword(),
            'salt' => $model->getSalt(),
            'roles' => $this->createStringFromArray($model->getRoles()),
            'first_name' => $model->getFirstName(),
            'last_name' => $model->getLastName()
        );

        return $keyValues;
    }

}