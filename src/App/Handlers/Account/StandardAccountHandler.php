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
use App\Validators\UserId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Validator\Constraints\NotBlank;

class StandardAccountHandler extends AbstractAccountHandler
{

    public function createAccount(Request $request)
    {
        $email = $request->request->get('email');
        $user = $this->getAccountByEmail($email);

        if($user) {
            $facebookInfoManager = $this->modelManagerFactory->getModelManager('facebookInfo', 'mysql');
            $facebookInfo = $facebookInfoManager->readModelOneBy(array(
                'user_id' => $user->getId()
            ));
            $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User', 'mysql');
            $oauth2User = $oauth2UserManager->readModelOneBy(array(
                'user_id' => $user->getId()
            ));

            if($facebookInfo && !$oauth2User) throw new AlreadyRegisteredException(true, [
                'email' => $user->getEmail(),
                'error_description' => "Hey ".$user->getFirstName().", you are already with us! But we offer you to merge this account with existing account. Say OK and you're in!"
            ]);

            else throw new AlreadyRegisteredException();
        }

        $password = $request->request->get('password');
        $firstName = $request->request->get('first_name');
        $lastName = $request->request->get('last_name');

        $salt = (new SaltGenerator())->generateSalt();
        $encodedPassword = $this->encodePassword($password, $salt);

        $userManager = $this->modelManagerFactory->getModelManager('user', 'mysql');
        $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User', 'mysql');

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

        return new JsonResponse(array(), 200);
    }

    public function mergeAccount(Request $request)
    {
        $userId = $request->request->get('user_id');
        $user = $this->getAccountById($userId);

        if(!$user) return new InvalidRequestException();

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

        $oauth2UserManager->createModel($oauth2User);

        return new JsonResponse(array(), 200);

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