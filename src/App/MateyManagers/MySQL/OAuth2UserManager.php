<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.43
 */

namespace App\MateyModels;


use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuth2UserManager extends AbstractManager implements UserProviderInterface
{
    public function __construct () {
        parent::__construct(self::T_A_USER, 'App\\MateyModels\\OAuth2User');
    }

    public function loadUserByUsername($username)
    {
        $result = $this->db->fetchAll("SELECT user_id, username, password, salt
        FROM ".self::T_A_USER."
        WHERE username = ? LIMIT 1",
            array($username));

        $models = $this->makeObjects($result);

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