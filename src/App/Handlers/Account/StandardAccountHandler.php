<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.11.16.
 * Time: 17.01
 */

namespace App\Handlers\Account;


use App\Exception\AlreadyRegisteredException;
use App\Security\SaltGenerator;
use App\Validators\Name;
use App\Validators\UserId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Validator\Constraints\NotBlank;

class StandardAccountHandler extends AbstractAccountHandler
{

    public function createAccount(Request $request)
    {
        /*
         * First, check if user is already registered
         */
        $email = trim($request->request->get('email'));
        $user = $this->getAccountByEmail($email);

        /*
         * If user haven't been found, variable will be empty array
         */
        if(!empty($user)) {
            /*
             * Fetch user facebook data
             */
            $facebookInfoManager = $this->modelManagerFactory->getModelManager('facebookInfo', 'mysql');
            $facebookInfo = $facebookInfoManager->readModelOneBy(array(
                'user_id' => $user->getId()
            ));

            /*
             * Fetch user oauth2 data
             */
            $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User', 'mysql');
            $oauth2User = $oauth2UserManager->readModelOneBy(array(
                'user_id' => $user->getId()
            ));

                /*
                 * If there is facebook account only
                 * offering merge
                 */
                if($facebookInfo && !$oauth2User) throw new AlreadyRegisteredException(true, [
                    'email' => $user->getEmail(),
                    'error_description' => "Hey ".$user->getFirstName().", you are already with us! But we offer you to merge this account with existing account. Say OK and you're in!"
                ]);
                /*
                 * If there is both accounts,
                 * than user is full registered already
                 */
                else throw new AlreadyRegisteredException();
        }

        $password = $request->request->get('password');
        $firstName = trim($request->request->get('first_name'));
        $lastName = trim($request->request->get('last_name'));

        $errors = $this->validator->validate($firstName, [
            new NotBlank(),
            new Name()
        ]);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        $errors = $this->validator->validate($lastName, [
            new NotBlank(),
            new Name()
        ]);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        /*
         * Prepare salt and encoded password for new user
         */
        $salt = (new SaltGenerator())->generateSalt();
        $encodedPassword = $this->encodePassword($password, $salt);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User');

        $userClass = $userManager->getClassName();
        $oauth2UserClass = $oauth2UserManager->getClassName();

        $user = new $userClass();
        $oauth2User = new $oauth2UserClass();

        $user->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setFullName($firstName." ".$lastName)
            ->setSilhouette(true);

        $oauth2User->setUsername($email)
            ->setPassword($encodedPassword)
            ->setSalt($salt);

        /*
         * Insert data into the database
         */
        $userManager->startTransaction();
        try {
            $user = $this->storeUserData($user);
            $oauth2User->setId($user->getId());
            $oauth2UserManager->createModel($oauth2User);

            $userManager->commitTransaction();
        } catch (\Exception $e) {
            $userManager->rollbackTransaction();
            throw new ServerErrorException();
        }

        return new JsonResponse(null, 200);
    }

    public function mergeAccount(Request $request)
    {
        /*
         * Check if user is really registered
         */
        $userId = $request->request->get('user_id');
        $user = $this->getAccountById($userId);

        if(empty($user)) return new ResourceNotFoundException();

        /*
         * Prepare password and salt
         */
        $password = $request->request->get('password');

        $salt = (new SaltGenerator())->generateSalt();
        $encodedPassword = $this->encodePassword($password, $salt);

        $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User', 'mysql');
        $oauth2UserClass = $oauth2UserManager->getClassName();
        $oauth2User = new $oauth2UserClass();

        $oauth2User->setId($user->getId())
            ->setUsername($user->getEmail())
            ->setPassword($encodedPassword)
            ->setSalt($salt);

        /*
         * Create oauth2 account credentials
         */
        $oauth2UserManager->createModel($oauth2User);

        return new JsonResponse(null, 200);

    }

    public function encodePassword($password, $salt) {

        $errors = $this->validator->validate($password, [
            new NotBlank(),
            new Password()
        ]);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        // encoding password and salt
        $passwordEncoder = new MessageDigestPasswordEncoder();
        return $passwordEncoder->encodePassword($password, $salt);
    }

}