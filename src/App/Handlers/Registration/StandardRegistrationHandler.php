<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 16.25
 */

namespace App\Handlers\Registration;


use App\MateyModels\OAuth2User;
use App\MateyModels\User;
use App\Security\SaltGenerator;
use App\Validators\Name;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Validator\Constraints\NotBlank;

class StandardRegistrationHandler extends AbstractRegistrationHandler
{
    public function handle(Request $request) {

        $username = $request->request->get('email');
        $user = $this->getUserCoreData($username);

        if($user) {
            $facebookInfoManager = $this->modelManagerFactory->getModelManager('facebookInfo', 'mysql');
            $facebookInfo = $facebookInfoManager->readModelOneBy(array(
                'user_id' => $user->getUserId()
            ));
            $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User', 'mysql');
            $oauth2User = $oauth2UserManager->readModelOneBy(array(
                'user_id' => $user->getUserId()
            ));
            if ($facebookInfo && $oauth2User) throw new InvalidRequestException([
                'error' => 'full_reg',
                'error_description' => 'Hey Mate, you are already with us!'
            ]);
            // If email is already registered, and facebook id is registered also,
            // it means that user have facebook account, but not standard
            else if($facebookInfo && !$oauth2User) throw new InvalidRequestException([
                'error' => 'merge_offer',
                'error_description' => "Hey ".$user->getFirstName().", you are already with us! But we offer you to merge this account with existing account. Say OK and you're in!"
            ], 409);
            //This shouldn't ever come true, but just in case.
            //In this case user will have to use another email to register
            else if($oauth2User) throw new InvalidRequestException([
                'error_description' => 'Hey '.$user->getFirstName().', you are already with us!'
            ]);
            else throw new ServerErrorException();
        }

        $password = $request->request->get('password');
        $firstName = $request->request->get('first_name');
        $lastName = $request->request->get('last_name');

        $errors = $this->validator->validate($password, [
            new NotBlank(),
            new Password()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // generating random salt
        $salt = (new SaltGenerator())->generateSalt();
        // encoding password and salt
        $passwordEncoder = new MessageDigestPasswordEncoder();
        $encodedPassword = $passwordEncoder->encodePassword($password, $salt);

        $userManager = $this->modelManagerFactory->getModelManager('user', 'mysql');
        $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User', 'mysql');

        $userClass = $userManager->getClassName();
        $oauth2UserClass = $oauth2UserManager->getClassName();

        $user = new $userClass();
        $oauth2User = new $oauth2UserClass();

        $user->setEmail($username)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setSilhouette(1);

        $oauth2User->setUsername($username)
            ->setPassword($encodedPassword)
            ->setSalt($salt);

        $userManager->startTransaction();
        try {
            $user = $this->storeUserCoreData($user);
            $oauth2User->setUserId($user->getId());
            $oauth2UserManager->createModel($oauth2User);

            $userManager->commitTransaction();
        } catch (\Exception $e) {
            $userManager->rollbackTransaction();
            throw $e;
        }
        return new JsonResponse(array(), 200);

    }
}