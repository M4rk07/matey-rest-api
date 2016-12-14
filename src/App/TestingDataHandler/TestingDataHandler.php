<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.12.16.
 * Time: 17.37
 */

namespace App\Handlers\TestingData;


use App\MateyModels\ModelManagerFactoryInterface;
use App\Security\SaltGenerator;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use Doctrine\DBAL\Connection;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestingDataHandler implements TestingDataHandlerInterface
{

    protected $validator;
    protected $modelManagerFactory;
    protected $db;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        Connection $db
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->db = $db;
    }

    public function fillUsersTable() {

        $usersData = file_get_contents(__DIR__.'/../../../test-data/matey_user.sql');
        $this->db->executeQuery($usersData);

    }

    public function makeRelationships () {

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->readModelAll(10);
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userClass = $userManager->getClassName();

        $userFrom = new $userClass();
        $userTo = new $userClass();



        $followManager = $this->modelManagerFactory->getModelManager('follow');
        $followClassName = $followManager->getClassName();
        $follow = new $followClassName();

        foreach($users as $user) {

            $haveBeen = array();
            $numOfFollowers = rand(50, 100);

            for($i=0; $i<$numOfFollowers; $i++) {

                $userId = rand(1, 1000);
                while($userId == $user->getId() || in_array($userId, $haveBeen)) $userId = rand(1, 1000);

                $follow->setUserFrom($userId)
                    ->setUserTo($user->getId());

                try {
                    $followManager->createModel($follow);

                    $userFrom->setId($userId);
                    $userManager->incrNumOfFollowers($user);
                    $userManager->incrNumOfFollowing($userFrom);
                } catch (\Exception $e) {}
                $haveBeen[] = $userId;

            }

            $haveBeen = array();
            $numOfFollowing = rand(50, 100);

            for($i=0; $i<$numOfFollowing; $i++) {

                $userId = rand(1, 1000);
                while($userId == $user->getId() || in_array($userId, $haveBeen)) $userId = rand(1, 1000);

                $follow->setUserFrom($user->getId())
                    ->setUserTo($userId);

                try {
                    $followManager->createModel($follow);

                    $userTo->setId($userId);
                    $userManager->incrNumOfFollowers($userTo);
                    $userManager->incrNumOfFollowing($user);
                } catch (\Exception $e) {}
                $haveBeen[] = $userId;

            }

        }

    }

    public function makeOAuth2Accounts()
    {
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->readModelAll(10);

        $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User');
        $oauth2UserClass = $oauth2UserManager->getClassName();
        $oauth2User = new $oauth2UserClass();

        foreach($users as $user) {

            $salt = (new SaltGenerator())->generateSalt();
            $encodedPassword = $this->encodePassword($user->getFullName(), $salt);

            $oauth2User->setUsername($user->getEmail())
                ->setPassword($encodedPassword)
                ->setSalt($salt);
            $oauth2User->setId($user->getId());

            $oauth2UserManager->createModel($oauth2User);
        }

    }

    public function encodePassword($password, $salt) {
        $errors = $this->validator->validate($password, [
            new NotBlank(),
            new Password()
        ]);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // encoding password and salt
        $passwordEncoder = new MessageDigestPasswordEncoder();
        return $passwordEncoder->encodePassword($password, $salt);
    }


}