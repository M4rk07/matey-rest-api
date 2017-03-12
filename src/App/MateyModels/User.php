<?php

namespace App\MateyModels;
use App\Constants\Defaults\DefaultDates;
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
    protected $userId;
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $fullName;
    protected $silhouette;
    protected $dateRegistered;
    protected $verified;
    protected $phoneNumber;
    protected $location;
    protected $country;
    protected $birthday;

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

    protected $feedForCalculation;
    protected $feedScored;
    protected $feedSeen;

    public function setId($id) {
        return $this->setUserId($id);
    }

    public function getId() {
        return $this->getUserId();
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
        $this->userId = (int)$userId;
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
    public function getProfilePicture($size = 'small')
    {
        $dimension = '100x100';
        if($size != 'small' && in_array($size, array('medium', 'large', 'veryLarge', 'original'))) {
            if($size == 'medium') $dimension = '200x200';
            else if($size == 'large') $dimension = '480x480';
            else if($size == 'original') $dimension = 'originals';
        }
        if($this->silhouette == 0) return "https://tctechcrunch2011.files.wordpress.com/2010/10/pirate.jpg";
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/pictures/".$dimension."/".$this->getId().".jpg";
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
        $this->silhouette = (int)$silhouette;
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
        $this->verified = (int)$verified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
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
    public function getNumOfGivenReplies()
    {
        return $this->numOfGivenResponses;
    }

    /**
     * @param mixed $numOfGivenResponses
     */
    public function setNumOfGivenReplies($numOfGivenResponses)
    {
        $this->numOfGivenResponses = (int)$numOfGivenResponses;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfReceivedReplies()
    {
        return $this->numOfReceivedResponses;
    }

    /**
     * @param mixed $numOfReceivedResponses
     */
    public function setNumOfReceivedReplies($numOfReceivedResponses)
    {
        $this->numOfReceivedResponses = (int)$numOfReceivedResponses;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfBestReplies()
    {
        return $this->numOfBestResponses;
    }

    /**
     * @param mixed $numOfBestResponses
     */
    public function setNumOfBestReplies($numOfBestResponses)
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

    public function getSetFunction (array $props, $type = 'get') {
        if($props['key'] == 'user_id') {
            if($type == 'get') return $this->getUserId();
            else return $this->setUserId($props['value']);
        }
        else if($props['key'] == 'email') {
            if($type == 'get') return $this->getEmail();
            else return $this->setEmail($props['value']);
        }
        else if($props['key'] == 'first_name') {
            if($type == 'get') return $this->getFirstName();
            else return $this->setFirstName($props['value']);
        }
        else if($props['key'] == 'last_name') {
            if($type == 'get') return $this->getLastName();
            else return $this->setLastName($props['value']);
        }
        else if($props['key'] == 'full_name') {
            if($type == 'get') return $this->getFullName();
            else return $this->setFullName($props['value']);
        }
        else if($props['key'] == 'is_silhouette') {
            if($type == 'get') return $this->isSilhouette();
            else return $this->setSilhouette($props['value']);
        }
        else if($props['key'] == 'verified') {
            if($type == 'get') return $this->getVerified();
            else return $this->setVerified($props['value']);
        }
        else if($props['key'] == 'date_registered') {
            if($type == 'get') return $this->getDateRegistered();
            else return $this->setDateRegistered($props['value']);
        }
        else if($props['key'] == 'location') {
            if($type == 'get') return $this->getLocation();
            else return $this->setLocation($props['value']);
        }
        else if($props['key'] == 'country') {
            if($type == 'get') return $this->getCountry();
            else return $this->setCountry($props['value']);
        }
        else if($props['key'] == 'birthday') {
            if($type == 'get') return $this->getBirthday();
            else return $this->setBirthday($props['value']);
        }
        else if($props['key'] == 'phone_number') {
            if($type == 'get') return $this->getPhoneNumber();
            else return $this->setPhoneNumber($props['value']);
        }
        else if($props['key'] == 'num_of_followers') {
            if($type == 'get') return $this->getNumOfFollowers();
            else return $this->setNumOfFollowers($props['value']);
        }
        else if($props['key'] == 'num_of_following') {
            if($type == 'get') return $this->getNumOfFollowing();
            else return $this->setNumOfFollowing($props['value']);
        }
        else if($props['key'] == 'num_of_posts') {
            if($type == 'get') return $this->getNumOfPosts();
            else return $this->setNumOfPosts($props['value']);
        }
        else if($props['key'] == 'num_of_given_approves') {
            if($type == 'get') return $this->getNumOfGivenApproves();
            else return $this->setNumOfGivenApproves($props['value']);
        }
        else if($props['key'] == 'num_of_received_approves') {
            if($type == 'get') return $this->getNumOfReceivedApproves();
            else return $this->setNumOfReceivedApproves($props['value']);
        }
        else if($props['key'] == 'num_of_given_replies') {
            if($type == 'get') return $this->getNumOfGivenReplies();
            else return $this->setNumOfGivenReplies($props['value']);
        }
        else if($props['key'] == 'num_of_received_replies') {
            if($type == 'get') return $this->getNumOfReceivedReplies();
            else return $this->setNumOfReceivedReplies($props['value']);
        }
        else if($props['key'] == 'num_of_best_replies') {
            if($type == 'get') return $this->getNumOfBestReplies();
            else return $this->setNumOfBestReplies($props['value']);
        }
        else if($props['key'] == 'num_of_shares') {
            if($type == 'get') return $this->getNumOfShares();
            else return $this->setNumOfShares($props['value']);
        }
        else if($props['key'] == 'picture_url') {
            if($type == 'get') return $this->getProfilePicture();
        }
    }

}