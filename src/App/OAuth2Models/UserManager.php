<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 6:50 PM
 */

namespace App\OAuth2Models;


use App\Services\BaseService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserManager extends BaseService implements UserProviderInterface
{
    public function __construct () {
        parent::__construct();
        $this->tableName = self::T_USER;
        $this->className = 'App\\OAuth2Models\\User';
        $this->identifier = "id_user";
    }

    public function deleteUser(UserInterface $user)
    {
        $this->db->delete(self::T_USER, array($this->identifier => $user->getId()));

        return $user;
    }

    public function reloadUser(UserInterface $user)
    {
        return $this->refreshUser($user);
    }

    public function updateUser(UserInterface $user)
    {
        $this->db->update(self::T_USER, $user->getValuesAsArray($user), array($this->identifier => $user->getId()));

        return $user;
    }

    public function loadUserByUsername($username)
    {
        $all = $this->db->fetchAll(
            "SELECT " . self::T_USER . ".*, "
            . self::T_STANDARD_USER . ".password, "
            . self::T_STANDARD_USER . ".salt
            FROM " . self::T_USER . "
            LEFT JOIN " . self::T_STANDARD_USER . " USING(id_user) 
            WHERE " . self::T_USER . ".email = ? LIMIT 1",
            array($username)
        );
        // make objects form result
        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function refreshUser(UserInterface $user)
    {
        $all = $this->db->fetchAll(
            "SELECT " . self::T_USER . ".*, "
            . self::T_STANDARD_USER . ".password, "
            . self::T_STANDARD_USER . ".salt
            FROM " . self::T_USER . "
            LEFT JOIN " . self::T_STANDARD_USER . " USING(id_user) 
            WHERE matey_user.id_user = ? LIMIT 1",
            array($user->getId()));
        // make objects form result
        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function supportsClass($class)
    {
        return $this->className === $class
        || is_subclass_of($class, $this->className);
    }

}