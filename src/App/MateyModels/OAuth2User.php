<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.43
 */

namespace App\MateyModels;


use App\OAuth2Models\AbstractModel;
use AuthBucket\OAuth2\Model\ModelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuth2User extends \App\MateyModels\AbstractModel implements UserInterface
{

    protected $userId;
    protected $username;
    protected $password;
    protected $salt;
    protected $roles = array();

    public function setId($id) {
        return $this->setUserId($id);
    }
    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

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
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getSetFunction (array $props, $type = 'get')
    {
        if ($props['key'] == 'user_id') {
            if ($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        } else if ($props['key'] == 'username') {
            if ($type == 'get') return $this->getUsername();
            else return $this->setUsername($props['value']);
        } else if ($props['key'] == 'password') {
            if ($type == 'get') return $this->getPassword();
            else return $this->setPassword($props['value']);
        } else if ($props['key'] == 'salt') {
            if ($type == 'get') return $this->getSalt();
            else return $this->setSalt($props['value']);
        }
    }


}