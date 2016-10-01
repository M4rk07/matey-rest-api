<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 6:50 PM
 */

namespace App\OAuth2Models;


use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserManager extends AbstractManager implements UserProviderInterface
{
    public function __construct () {
        parent::__construct();
        $this->tableName = "matey_user";
        $this->className = 'App\\OAuth2Models\\User';
        $this->identifier = "id_user";
    }

    public function deleteUser(UserInterface $user)
    {
        $this->db->delete($this->tableName, array($this->identifier => $user->getId()));

        return $user;
    }

    public function reloadUser(UserInterface $user)
    {
        return $this->refreshUser($user);
    }

    public function updateUser(UserInterface $user)
    {
        $this->db->update($this->tableName, $user->getValuesAsArray($user), array($this->identifier => $user->getId()));

        return $user;
    }

    public function loadUserByUsername($username)
    {
        $all = $this->db->fetchAll("SELECT matey_user.*, matey_standard_user.password, matey_standard_user.salt
        FROM " . $this->tableName . "
        LEFT JOIN matey_standard_user USING(id_user) WHERE matey_user.email = ? LIMIT 1",
            array($username));
        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function refreshUser(UserInterface $user)
    {
        $all = $this->db->fetchAll("SELECT matey_user.*, matey_standard_user.password, matey_standard_user.salt
        FROM " . $this->tableName . "
        LEFT JOIN matey_standard_user ON matey_user.id_user = matey_standard_user.id_user WHERE matey_user.id_user = ? LIMIT 1",
            array($user->getId()));
        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function supportsClass($class)
    {
        return $this->className === $class
        || is_subclass_of($class, $this->className);
    }

}