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

    public function setValuesFromArray($values)
    {
        $this->userId = isset($values['user_id']) ? $values['user_id'] : "";
        $this->username = isset($values['username']) ? $values['username'] : "";
        $this->password = isset($values['password']) ? $values['password'] : "";
        $this->salt = isset($values['salt']) ? $values['salt'] : "";
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->username) ? : $keyValues['username'] = $this->username;
        empty($this->password) ? : $keyValues['password'] = $this->password;
        empty($this->salt) ? : $keyValues['salt'] = $this->salt;

        return $keyValues;
    }

    public function getValuesAsArray()
    {
        $keyValues = array ();

        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->username) ? : $keyValues['username'] = $this->username;
        empty($this->password) ? : $keyValues['password'] = $this->password;
        empty($this->salt) ? : $keyValues['salt'] = $this->salt;

        return $keyValues;
    }


}