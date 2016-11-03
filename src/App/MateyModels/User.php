<?php

namespace App\MateyModels;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 00.02
 */
class User implements UserInterface
{

    protected $userId;
    protected $username;
    protected $password;
    protected $salt;
    protected $roles = array();
    protected $firstName;
    protected $lastName;
    protected $fullName;
    protected $profilePicture;
    protected $silhouette;
    protected $firstLogin;
    protected $dateRegistered;
    protected $followers;
    protected $following;
    protected $fbId;
    protected $fbToken;
    protected $activities = array();
    protected $newsfeeds = array();

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
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * @param mixed $fbId
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;
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
     * @return array
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * @param array $activities
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isFirstLogin()
    {
        return $this->firstLogin;
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
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param mixed $followers
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * @param mixed $following
     */
    public function setFollowing($following)
    {
        $this->following = $following;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getFbToken()
    {
        return $this->fbToken;
    }

    /**
     * @param mixed $fbToken
     */
    public function setFbToken($fbToken)
    {
        $this->fbToken = $fbToken;
        return $this;
    }

    /**
     * @return array
     */
    public function getNewsfeeds()
    {
        return $this->newsfeeds;
    }

    /**
     * @param array $newsfeeds
     */
    public function setNewsfeeds(array $newsfeeds)
    {
        $this->newsfeeds = $newsfeeds;
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

    public function isFacebookAccount() {
        return empty($this->fbId) ? false : true;
    }

    public function isStandardAccount() {
        return empty($this->password) ? false : true;
    }


    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

}