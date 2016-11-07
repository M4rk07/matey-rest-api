<?php

namespace App\MateyModels;
use AuthBucket\OAuth2\Model\ModelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 00.02
 */
class User extends AbstractModel
{

    protected $userId;
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $fullName;
    protected $profilePicture;
    protected $silhouette;
    protected $firstLogin;
    protected $dateRegistered;

    // STATISTICS
    protected $numOfFollowers;
    protected $numOfFollowing;
    protected $numOfPosts;
    protected $numOfGivenApproves;
    protected $numOfReceivedApproves;
    protected $numOfGivenResponses;
    protected $numOfReceivedResponses;
    protected $numOfBestResponses;
    protected $numOfProfileClicks;
    protected $numOfShares;

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param mixed $profilePicture
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isSilhouette()
    {
        return $this->silhouette;
    }

    /**
     * @param mixed $silhouette
     */
    public function setSilhouette($silhouette)
    {
        $this->silhouette = $silhouette;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isFirstLogin()
    {
        return $this->firstLogin == 0 ? true : false;
    }

    /**
     * @param mixed $firstLogin
     */
    public function setFirstLogin($firstLogin)
    {
        $this->firstLogin = $firstLogin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateRegistered()
    {
        return $this->dateRegistered;
    }

    /**
     * @param mixed $dateRegistered
     */
    public function setDateRegistered($dateRegistered)
    {
        $this->dateRegistered = $dateRegistered;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfFollowers()
    {
        return $this->numOfFollowers;
    }

    /**
     * @param mixed $numOfFollowers
     */
    public function setNumOfFollowers($numOfFollowers)
    {
        $this->numOfFollowers = $numOfFollowers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfFollowing()
    {
        return $this->numOfFollowing;
    }

    /**
     * @param mixed $numOfFollowing
     */
    public function setNumOfFollowing($numOfFollowing)
    {
        $this->numOfFollowing = $numOfFollowing;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfPosts()
    {
        return $this->numOfPosts;
    }

    /**
     * @param mixed $numOfPosts
     */
    public function setNumOfPosts($numOfPosts)
    {
        $this->numOfPosts = $numOfPosts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfGivenApproves()
    {
        return $this->numOfGivenApproves;
    }

    /**
     * @param mixed $numOfGivenApproves
     */
    public function setNumOfGivenApproves($numOfGivenApproves)
    {
        $this->numOfGivenApproves = $numOfGivenApproves;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfReceivedApproves()
    {
        return $this->numOfReceivedApproves;
    }

    /**
     * @param mixed $numOfReceivedApproves
     */
    public function setNumOfReceivedApproves($numOfReceivedApproves)
    {
        $this->numOfReceivedApproves = $numOfReceivedApproves;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfGivenResponses()
    {
        return $this->numOfGivenResponses;
    }

    /**
     * @param mixed $numOfGivenResponses
     */
    public function setNumOfGivenResponses($numOfGivenResponses)
    {
        $this->numOfGivenResponses = $numOfGivenResponses;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfReceivedResponses()
    {
        return $this->numOfReceivedResponses;
    }

    /**
     * @param mixed $numOfReceivedResponses
     */
    public function setNumOfReceivedResponses($numOfReceivedResponses)
    {
        $this->numOfReceivedResponses = $numOfReceivedResponses;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfBestResponses()
    {
        return $this->numOfBestResponses;
    }

    /**
     * @param mixed $numOfBestResponses
     */
    public function setNumOfBestResponses($numOfBestResponses)
    {
        $this->numOfBestResponses = $numOfBestResponses;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfProfileClicks()
    {
        return $this->numOfProfileClicks;
    }

    /**
     * @param mixed $numOfProfileClicks
     */
    public function setNumOfProfileClicks($numOfProfileClicks)
    {
        $this->numOfProfileClicks = $numOfProfileClicks;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfShares()
    {
        return $this->numOfShares;
    }

    /**
     * @param mixed $numOfShares
     */
    public function setNumOfShares($numOfShares)
    {
        $this->numOfShares = $numOfShares;
        return $this;
    }

    public function setValuesFromArray($values)
    {

        $this->userId = isset($values['user_id']) ? $values['user_id'] : "";
        $this->email = isset($values['email']) ? $values['email'] : "";
        $this->firstName = isset($values['first_name']) ? $values['first_name'] : "";
        $this->lastName = isset($values['last_name']) ? $values['last_name'] : "";
        $this->fullName = isset($values['full_name']) ? $values['full_name'] : "";
        $this->silhouette = isset($values['is_silhouette']) ? $values['is_silhouette'] : "";
        $this->dateRegistered = isset($values['date_registered']) ? $values['date_registered'] : "";

    }

    public function getValuesAsArray()
    {
        $keyValues = array ();

        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->email) ? : $keyValues['email'] = $this->email;
        empty($this->firstName) ? : $keyValues['first_name'] = $this->firstName;
        empty($this->lastName) ? : $keyValues['last_name'] = $this->lastName;
        empty($this->fullName) ? : $keyValues['full_name'] = $this->fullName;
        empty($this->silhouette) ? : $keyValues['is_silhouette'] = $this->silhouette;

        return $keyValues;
    }


}