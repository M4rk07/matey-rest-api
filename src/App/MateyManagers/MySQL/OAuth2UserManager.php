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
    public function __construct ($db) {
        parent::__construct($db);
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\OAuth2User';
    }

    public function getTableName() {
        return self::T_A_USER;
    }

    public function loadUserByUsername($username)
    {
        $result = $this->db->fetchAll("SELECT *
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