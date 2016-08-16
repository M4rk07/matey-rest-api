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
        $this->tableName = "users";
        $this->className = 'App\\OAuth2Models\\User';
    }

    public function loadUserByUsername($username)
    {
        $all = $this->db->fetchAll("SELECT * FROM " . $this->tableName . " WHERE email = ? LIMIT 1",
            array($username));
        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function refreshUser(UserInterface $user)
    {
        // TODO: Implement refreshUser() method.
    }

    public function supportsClass($class)
    {
        // TODO: Implement supportsClass() method.
    }

}