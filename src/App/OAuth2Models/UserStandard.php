<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.10.16.
 * Time: 23.25
 */

namespace App\OAuth2Models;


use Symfony\Component\Security\Core\User\UserInterface;

class UserStandard extends User implements UserInterface
{

    protected $password;
    protected $salt;

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

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

}