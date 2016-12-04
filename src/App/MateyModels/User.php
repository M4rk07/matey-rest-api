<?php

namespace App\MateyModels;
use App\Paths\Paths;
use App\Validators\Name;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 00.02
 */
class User extends AbstractModel
{
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $fullName;
    protected $silhouette;
    protected $dateRegistered;
    protected $verified;

    // STATISTICS
    protected $numOfFollowers;
    protected $numOfFollowing;
    protected $numOfPosts;
    protected $numOfGivenApproves;
    protected $numOfReceivedApproves;
    protected $numOfGivenResponses;
    protected $numOfReceivedResponses;
    protected $numOfBestResponses;
    protected $numOfShares;

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
    public function getProfilePicture($size = 'small')
    {
        $dimension = '100x100';
        if($size != 'small' && in_array($size, array('medium', 'large', 'veryLarge'))) {
            if($size == 'medium') $dimension = '200x200';
            else if($size == 'large') $dimension = '480x480';
            else if($size == 'veryLarge') $dimension = '720x720';
        }
        if($this->silhouette == 1) return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/profile_pictures/".$dimension."/silhouette.jpg";
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/profile_pictures/".$dimension."/".$this->getId().".jpg";
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
        $this->silhouette = (bool)$silhouette;
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
        $this->dateRegistered = $this->createDateTimeFromString($dateRegistered);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * @param mixed $verified
     */
    public function setVerified($verified)
    {
        $this->verified = (bool)$verified;
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
        $this->numOfFollowers = (int)$numOfFollowers;
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
        $this->numOfFollowing = (int)$numOfFollowing;
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
        $this->numOfPosts = (int)$numOfPosts;
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
        $this->numOfGivenApproves = (int)$numOfGivenApproves;
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
        $this->numOfReceivedApproves = (int)$numOfReceivedApproves;
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
        $this->numOfGivenResponses = (int)$numOfGivenResponses;
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
        $this->numOfReceivedResponses = (int)$numOfReceivedResponses;
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
        $this->numOfBestResponses = (int)$numOfBestResponses;
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
        $this->numOfShares = (int)$numOfShares;
        return $this;
    }

    public function setValuesFromArray($values)
    {

        if(isset($values['user_id'])) $this->setId($values['user_id']);
        if(isset($values['email'])) $this->setEmail($values['email']);
        if(isset($values['first_name'])) $this->setFirstName($values['first_name']);
        if(isset($values['last_name'])) $this->setLastName($values['last_name']);
        if(isset($values['full_name'])) $this->setFullName($values['full_name']);
        if(isset($values['is_silhouette'])) $this->setSilhouette($values['is_silhouette']);
        if(isset($values['verified'])) $this->setVerified($values['verified']);
        if(isset($values['date_registered'])) $this->setDateRegistered($values['date_registered']);
        if(isset($values['num_of_followers'])) $this->setNumOfFollowers($values['num_of_followers']);
        if(isset($values['num_of_following'])) $this->setNumOfFollowing($values['num_of_following']);
        if(isset($values['num_of_posts'])) $this->setNumOfPosts($values['num_of_posts']);
        if(isset($values['num_of_given_approves'])) $this->setNumOfGivenApproves($values['num_of_given_approves']);
        if(isset($values['num_of_received_approves'])) $this->setNumOfReceivedApproves($values['num_of_received_approves']);
        if(isset($values['num_of_given_responses'])) $this->setNumOfGivenResponses($values['num_of_given_responses']);
        if(isset($values['num_of_received_responses'])) $this->setNumOfReceivedResponses($values['num_of_received_responses']);
        if(isset($values['num_of_best_responses'])) $this->setNumOfBestResponses($values['num_of_best_responses']);
        if(isset($values['num_of_shares'])) $this->setNumOfShares($values['num_of_shares']);

    }

    public function getMysqlValues () {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['user_id'] = $this->id;
        empty($this->email) ? : $keyValues['email'] = $this->email;
        empty($this->firstName) ? : $keyValues['first_name'] = $this->firstName;
        empty($this->lastName) ? : $keyValues['last_name'] = $this->lastName;
        empty($this->fullName) ? : $keyValues['full_name'] = $this->fullName;
        empty($this->silhouette) && $this->silhouette != false ? : $keyValues['is_silhouette'] = $this->silhouette;
        empty($this->verified) ? : $keyValues['verified'] = $this->verified;

        return $keyValues;
    }

    public function getValuesAsArray($fields = null)
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['user_id'] = $this->id;
        empty($this->email) ? : $keyValues['email'] = $this->email;
        empty($this->firstName) ? : $keyValues['first_name'] = $this->firstName;
        empty($this->lastName) ? : $keyValues['last_name'] = $this->lastName;
        empty($this->fullName) ? : $keyValues['full_name'] = $this->fullName;
        empty($this->verified) && $this->verified != false ? : $keyValues['verified'] = $this->verified;
        //$keyValues['profile_picture'] = $this->getProfilePicture();
        $keyValues['picture_url'] = "https://tctechcrunch2011.files.wordpress.com/2010/10/pirate.jpg";
        $keyValues['cover_url'] = "http://vignette2.wikia.nocookie.net/angrybirds/images/c/cf/Heikki_wallpaper3_medium.jpg/revision/latest?cb=20120626123135";
        empty($this->numOfFollowers) && $this->numOfFollowers != 0 ? : $keyValues['num_of_followers'] = $this->numOfFollowers;
        empty($this->numOfFollowing) && $this->numOfFollowing != 0 ? : $keyValues['num_of_following'] = $this->numOfFollowing;
        empty($this->numOfPosts) && $this->numOfPosts != 0 ? : $keyValues['num_of_posts'] = $this->numOfPosts;
        empty($this->numOfGivenApproves) && $this->numOfGivenApproves != 0 ? : $keyValues['num_of_given_approves'] = $this->numOfGivenApproves;
        empty($this->numOfReceivedApproves) && $this->numOfReceivedApproves != 0 ? : $keyValues['num_of_received_approves'] = $this->numOfReceivedApproves;
        empty($this->numOfGivenResponses) && $this->numOfGivenResponses != 0 ? : $keyValues['num_of_given_responses'] = $this->numOfGivenResponses;
        empty($this->numOfReceivedResponses) && $this->numOfReceivedResponses != 0 ? : $keyValues['num_of_received_responses'] = $this->numOfReceivedResponses;
        empty($this->numOfBestResponses) && $this->numOfBestResponses != 0 ? : $keyValues['num_of_best_responses'] = $this->numOfBestResponses;
        empty($this->numOfShares) && $this->numOfShares != 0 ? : $keyValues['num_of_shares'] = $this->numOfShares;

        return $keyValues;
    }


}