<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 16.25
 */

namespace Matey\Handlers\Registration;


use App\Validators\Name;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class StandardRegistrationHandler extends AbstractRegistrationHandler
{
    public function handle(Request $request) {

        $username = $request->request->get('email');
        $user = $this->getUserCoreData($username);

        if(!empty((array)$user)) {
            // If email is already registered, and facebook id is registered also,
            // it means that user have facebook account, but not standard
            if($user->isFacebookAccount() && !$user->isStandardAccount()) throw new InvalidRequestException([
                'error' => 'merge_offer',
                'error_description' => "Hey ".$user->getFirstName().", you are already with us! But we offer you to merge this account with existing account. Say OK and you're in!"
            ], 409);
            //This shouldn't ever come true, but just in case.
            //In this case user will have to use another email to register
            else if($user->isStandardAccount() && !$user->isFacebookAccount()) throw new InvalidRequestException([
                'error_description' => 'Hey '.$user->getFirstName().', you are already with us!'
            ]);
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
        $errors = $this->validator->validate($firstName, [
            new NotBlank(),
            new Name()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }
        $errors = $this->validator->validate($lastName, [
            new NotBlank(),
            new Name()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $user->setUsername($username)
            ->setPassword($password)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setFullName($firstName." ".$lastName)
            ->setSilhouette(1);

        $this->userProvider->startTransaction();
        try {
            $this->storeUserCoreData($user);
            $this->userProvider->createUserCredentials($user, $password);

            $this->userProvider->commitTransaction();
        } catch (\Exception $e) {
            $this->userProvider->rollbackTransaction();
        }

        return new JsonResponse(array(), 200);

    }
}